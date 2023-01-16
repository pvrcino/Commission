<?php

namespace App\Http\Controllers;

use App\Models\Afiliacao;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdutosController extends Controller
{
    public function index()
    {
        $afiliacoes = Afiliacao::where('user_id', Auth::user()->id)->get();
        $afiliacoesSubSeller = Afiliacao::whereJsonContains('subseller', ["id" => Auth::user()->id])->get();

        $meusProdutos = [];
        $produtoSubseller = [];

        foreach ($afiliacoes as $afiliacao) {
            $produto = Produto::where("id", $afiliacao->produto_id)->first();
            $produto->comissao = $afiliacao->comissao;
            $produto->link = $afiliacao->link;
            $produto->afiliacao = $afiliacao->id;
            if ($afiliacao->subseller) {
                $subsellers = $afiliacao->subseller;
                $usersSubsellers = [];
                foreach ($subsellers as $subseller) {
                    $userSub = User::find($subseller["id"]);
                    $userSub->comissao = $subseller["comissao"];
                    $usersSubsellers[] = $userSub;
                }
                $produto->usersSubsellers = $usersSubsellers;

            }

            $meusProdutos[] = $produto;
        }

        foreach ($afiliacoesSubSeller as $afiliacao) {
            $produto = Produto::where("id", $afiliacao->produto_id)->first();
            $subsellers = $afiliacao->subseller;
            foreach ($subsellers as $subseller) {
                if ($subseller["id"] == Auth::user()->id) {
                    $produto->comissao = $subseller["comissao"];
                    $produto->link = $afiliacao->link;
                }
            }
            $produto->link = $afiliacao->link;
            $produtoSubseller[] = $produto;
        }

        return view("produtos.index")->with("meusProdutos", $meusProdutos)->with("produtoSubseller", $produtoSubseller);
    }
}
