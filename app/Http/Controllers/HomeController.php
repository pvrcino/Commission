<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index() {
        $metricas = Cache::remember('vendas_metricas_user' . Auth::user()->id, 30*60, function () {
            $vendasMesList = Venda::where('user_id', Auth::user()->id)->orWhereJsonContains('subseller', ["id" => Auth::user()->id])->where("status", "=", 1)->where("created_at", ">", Carbon::now("America/Sao_Paulo")->format('Y-m-01 00:00:00'))->get();
            $vendasMes = 0;
            $vendasDia = 0;

            foreach ($vendasMesList as $venda) {
                $comissao = $venda->comissao;
                if ($venda->subseller) {
                    foreach ($venda->subseller as $subseller) {
                        if ($subseller['id'] == Auth::user()->id) {
                            $comissao = $subseller['comissao'];
                            break;
                        }
                        $comissao -= $subseller["comissao"];
                    }
                }
                if (Carbon::create($venda->created_at)->format('Y-m-d') == Carbon::now("America/Sao_Paulo")->format('Y-m-d')) {
                    $vendasDia += $comissao;
                }
                $vendasMes += $comissao;
            }
            return [
                "vendasDia" => $vendasDia,
                "vendasMes" => $vendasMes
            ];
        });

        $vendasDia = $metricas["vendasDia"];
        $vendasMes = $metricas["vendasMes"];
        
        $vendasDia = number_format($vendasDia, 2, ',', '.');
        $vendasMes = number_format($vendasMes, 2, ',', '.');
        $vendas4Dias = DB::select("SELECT DATE(created_at) as data,SUM(comissao) as ganhos FROM vendas WHERE user_id = ? AND status = 1 AND created_at > (NOW() - INTERVAL 4 DAY) GROUP BY DATE(created_at) ORDER BY DATE(created_at)", [Auth::user()->id]);
        return view("home.index")->with("vendasDia", $vendasDia)->with("vendasMes", $vendasMes)->with("vendas4Dias", $vendas4Dias);
    }
}