<?php

namespace App\Console;

use App\Models\Afiliacao;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $vendasAddSaldo = Venda::where("status", 1)->where("addSaldo", 0)->get();
            foreach ($vendasAddSaldo as $venda) {
                $produto = Produto::find($venda->produto_id);
                $af = Afiliacao::where("user_id", $venda->user_id)->where("produto_id", $venda->produto_id)->first();
                if ($produto->plataforma == "Abmex" && $venda->paid_at < Carbon::now("America/Sao_Paulo")->subDays(7)) {
                    $venda->addSaldo = 1;
                    $venda->save();
                    $vendedor = User::find($venda->user_id);
                    
                    $subsellres = $af->subsellres;
                    $comissao = $venda->comissao;
                    foreach ($subsellres as $subseller) {
                        $user = User::find($subseller['id']);
                        $user->saldoDisponivel += $subseller['comissao'];
                        $user->saldoPendente -= $comissao;
                        $comissao -= $subseller['comissao'];
                        $user->save();
                        Cache::forget('vendas_user' . $user->id);
                        Cache::forget('vendas_metricas_user' . $user->id);
                    }
                    $vendedor->saldoDisponivel += $comissao;
                    $vendedor->saldoPendente -= $comissao;
                    $vendedor->save();
                    Cache::forget('vendas_user' . $vendedor->id);
                    Cache::forget('vendas_metricas_user' . $vendedor->id);
                }
            }
        })->daily();

        $schedule->call(function () {
            Venda::where("status", 0)->where("created_at", "<", Carbon::now("America/Sao_Paulo")->subMinutes(30))->update(["status" => 2]);
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
