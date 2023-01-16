<?php

namespace App\Jobs;

use App\Models\Afiliacao;
use App\Models\User;
use App\Models\Venda;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPepper implements ShouldQueue
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $afiliacao = Afiliacao::where("plan_id", $this->payload["off"])->first();

        $venda = Venda::where('transaction_code', $this->payload["transaction"])->first();
        if (!$venda) {
            $venda = new Venda();
            $venda->user_id = $afiliacao->user_id;
            $venda->afiliacao_id = $afiliacao->id;
            $venda->produto_id = $afiliacao->produto_id;
            $venda->transaction_code = $this->payload["transaction"];
            $venda->comprador = $this->payload["name"];
            $venda->valor = $this->payload["full_price"];
            $venda->comissao = $afiliacao->comissao;
            $venda->created_at = $this->payload["purchase_date"];
            $venda->status = 0;
            if ($this->payload['payment_type'] == "Card") {
                $venda->payment_type = 1;
            } else if ($this->payload['payment_type'] == "Pix") {
                $venda->payment_type = 2;
            } else if ($this->payload['payment_type'] == "Billet") {
                $venda->payment_type = 3;
            }
        }
        if ($this->payload['status'] == 'Paid') {
            $venda->status = 1;
            $venda->payed_at = $this->payload['confirmation_purchase_date'];
            if ($venda->payment_type == 1) {
                $user = User::find($afiliacao->user_id);
                $user->saldoPendente += $afiliacao->comissao;
                $user->save();
                $venda->addSaldo = false;
            }
            if ($venda->payment_type == 2) {
                $user = User::find($afiliacao->user_id);
                $user->saldoDisponivel += $afiliacao->comissao;
                $user->save();
            }
        } else if ($this->payload['status'] == 'Refunded') {
            $user = User::find($afiliacao->user_id);
            $user->saldoDisponivel -= $afiliacao->comissao;
            $user->save();
            $venda->status = 3;
        }else if ($this->payload['status'] == 'Refused') {
            $venda->status = 2;
        } else {
            $venda->status = 0;
        }
        $venda->transaction_code = $this->payload["transaction"];
        

    }
}
