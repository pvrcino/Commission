<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view("auth.login");
    }

    public function loginRequest(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "senha" => "required"
        ]);
        $user = User::where("email", $request->email)->first();
        if (!$user) {
            return redirect()->back()->withErrors(["email" => "Email ou senha incorretos"]);
        }
        if (!Hash::check($request->senha, $user->senha)) {
            return redirect()->back()->withErrors(["email" => "Email ou senha incorretos"]);
        }
        Auth::login($user);
        return redirect()->route("home.index");
    }

    public function register()
    {
        return view("auth.register");
    }

    public function registerRequest(Request $request)
    {
        $request->validate([
            "nome" => "required",
            "email" => "required|email|unique:users",
            "senha" => "required|min:8|max:16",
            "document" => "required|numeric|digits:11|unique:users",
            "telefone" => "required|numeric|digits:11",
            "senhaConfirmada" => "required|same:senha"
        ], [
            "nome.required" => "O campo nome é obrigatório",
            "email.required" => "O campo email é obrigatório",
            "email.email" => "O campo email deve ser um email válido",
            "email.unique" => "O email informado já está em uso",
            "senha.required" => "O campo senha é obrigatório",
            "senha.min" => "A senha deve ter no mínimo 8 caracteres",
            "senha.max" => "A senha deve ter no máximo 16 caracteres",
            "document.required" => "O campo CPF é obrigatório",
            "document.numeric" => "O campo CPF deve conter apenas números",
            "document.digits" => "O campo CPF deve conter 11 dígitos",
            "document.unique" => "O CPF informado já está em uso",
            "telefone.required" => "O campo telefone é obrigatório",
            "telefone.numeric" => "O campo telefone deve conter apenas números",
            "telefone.digits" => "O campo telefone deve conter 11 dígitos",
            "senhaConfirmada.required" => "O campo confirmação de senha é obrigatório",
            "senhaConfirmada.same" => "A confirmação de senha deve ser igual a senha"
        ]);
        if (!str_contains($request->nome, " ")) {
            return redirect()->back()->withErrors(["nome" => "Insira o seu nome completo!"]);
        }
        $user = new User();
        $user->nome = $request->nome;
        $user->email = $request->email;
        $user->document = $request->document;
        $user->telefone = $request->telefone;
        $user->senha = Hash::make($request->senha);
        $user->save();
        Auth::login($user, true);
        return redirect()->route("home.index")->with("success", ["Usuário criado com sucesso"]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route("auth.login");
    }
}
