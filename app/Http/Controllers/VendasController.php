<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAbmex;
use Illuminate\Http\Request;
use App\Jobs\ProcessPepper;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VendasController extends Controller
{
    public function pepper(Request $request) {
        ProcessPepper::dispatch($request->all())->onQueue("pepper");
        return response()->json(["status" => "ok"]);
    }

    public function abmex(Request $request) {
        ProcessAbmex::dispatch($request->all())->onQueue("abmex");
        return response()->json(["status" => "ok"]);
    }

    public function index() {
        $vendas = Cache::remember('vendas_user' . Auth::user()->id, 30*60, function () {
            $vendas = Venda::where('user_id', Auth::user()->id)->orWhereJsonContains('subseller', ["id" => Auth::user()->id])->orderBy("created_at","desc")->paginate(20);
            foreach ($vendas as $venda) {
                $venda->produto = Produto::find($venda->produto_id);
                if ($venda->subseller) {
                    $comissao = $venda->comissao;
                    foreach ($venda->subseller as $subseller) {
                        if ($subseller['id'] == Auth::user()->id) {
                            $comissao = $subseller['comissao'];
                            break;
                        }
                        $comissao -= $subseller["comissao"];
                    }
                    $venda->comissao = $comissao;
                }
            } 
            return $vendas;
        });
        return view("vendas.index")->with("vendas", $vendas);
    }
}
