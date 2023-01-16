<?php

namespace App\Http\Controllers;

use App\Models\Afiliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AfiliacaoController extends Controller
{
    public function index() {
        return view("afiliacao.index");
    }

    public function redirect($id) {
        $afiliacao = Afiliacao::find($id);
        if (!$afiliacao) {
            return response("Erro: Afiliacao não encontrada", 404);
        }
        $link = $afiliacao->link;
        return redirect($link);
    }

    
}
