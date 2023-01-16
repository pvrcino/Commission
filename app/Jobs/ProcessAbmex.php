<?php

namespace App\Jobs;

use App\Models\Afiliacao;
use App\Models\User;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProcessAbmex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function tags()
    {
        return ['abmex', $this->payload['sell']['id']];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $afiliacao = Afiliacao::where("plan_id", $this->payload["product"]['id'])->first();

        $venda = Venda::where('transaction_code', $this->payload["sell"]['id'])->first();
        if (!$venda) {
            $venda = new Venda();
            $venda->user_id = $afiliacao->user_id;
            $venda->afiliacao_id = $afiliacao->id;
            $venda->produto_id = $afiliacao->produto_id;
            $venda->transaction_code = $this->payload["sell"]['id'];
            $venda->comprador = $this->payload["buyer"]["name"];
            $venda->valor = $this->payload["sell"]["sell_total_value"] / 100;

            $venda->comissao = $afiliacao->comissao;
            if ($afiliacao->subseller) {
                $subsellers = $afiliacao->subseller;
                $venda->subseller = $subsellers;
            }

            $sell_date = Carbon::createFromTimeString($this->payload["sell"]["sell_date"]);
            $sell_date = $sell_date->addHours(-3);

            $venda->created_at = $sell_date;
            $venda->status = 0;
            if ($this->payload['payment'] == "credit_card") {
                $venda->payment_type = 1;
            } else if ($this->payload['payment'] == "pix") {
                $venda->payment_type = 2;
            } else if ($this->payload['payment'] == "bank_slip") {
                $venda->payment_type = 3;
            }
            $venda->save();
        }
        $venda->refresh();
        if ($this->payload["sell"]['sell_status'] == 'paid') {
            if ($venda->status != 1) {
                $venda->status = 1;
                $venda->paid_at = Carbon::now("America/Sao_Paulo");
                if ($venda->payment_type == 1) {

                    if (!$venda->subseller) {
                        $user = User::find($afiliacao->user_id);
                        $user->saldoPendente += $venda->comissao;
                        $user->save();
                    } else {
                        $comissao = $venda->comissao;
                        $subsellers = $venda->subseller;
                        foreach ($subsellers as $subseller) {
                            $user = User::find($subseller['id']);
                            $user->saldoPendente += $subseller['comissao'];
                            $comissao -= $subseller['comissao'];
                            $user->save();
                            Cache::forget('vendas_user' . $user->id);
                            Cache::forget('vendas_metricas_user' . $user->id);
                        }
                        $user = User::find($afiliacao->user_id);
                        $user->saldoPendente += $comissao;
                        $user->save();
                    }
                    
                    $venda->addSaldo = false;
                }
                if ($venda->payment_type == 2) {

                    if (!$venda->subseller) {
                        $user = User::find($afiliacao->user_id);
                        $user->saldoDisponivel += $venda->comissao;
                        $user->save();
                    } else {
                        $comissao = $venda->comissao;
                        $subsellers = $venda->subseller;
                        foreach ($subsellers as $subseller) {
                            $user = User::find($subseller['id']);
                            $user->saldoDisponivel += $subseller['comissao'];
                            $comissao -= $subseller['comissao'];
                            $user->save();
                            Cache::forget('vendas_user' . $user->id);
                            Cache::forget('vendas_metricas_user' . $user->id);
                        }
                        $user = User::find($afiliacao->user_id);
                        $user->saldoDisponivel += $comissao;
                        $user->save();
                    }
                    

                }
                $comissions = $this->payload["commissions"];
                foreach ($comissions as $comission) {
                    if ($comission["user_type"] == "producer") {
                        $venda->comTaxas = $comission["commission_value"] / 100;
                        break;
                    }
                }
            }
        } else if ($this->payload["sell"]['sell_status'] == 'refunded' || $this->payload["sell"]['sell_status'] == 'chargeback') {
            if ($venda->status != 3) {
                $user = User::find($afiliacao->user_id);

                $comissao = $venda->comissao;
                $subsellers = $venda->subsellers;
                foreach ($subsellers as $subseller) {
                    $user = User::find($subseller['user_id']);
                    $user->saldoDisponivel -= $subseller['comissao'];
                    $comissao -= $subseller['comissao'];
                    $user->save();
                    Cache::forget('vendas_user' . $user->id);
                    Cache::forget('vendas_metricas_user' . $user->id);
                }
                $user = User::find($afiliacao->user_id);
                $user->saldoDisponivel -= $comissao;

                $user->save();
                $venda->status = 3;
            }
        } else if ($this->payload["sell"]['sell_status'] == 'canceled' || $this->payload["sell"]['sell_status'] == 'expired') {
            $venda->status = 2;
        } else {
            $venda->status = 0;
        }
        $venda->save();
        Cache::forget('vendas_user' . $venda->user_id);
        Cache::forget('vendas_metricas_user' . $venda->user_id);
        Cache::forget("lucros");
    }
}
