<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Saque;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SaqueController extends Controller
{
    public function index() {
        $saques = Saque::where("user_id", Auth::user()->id)->orderBy("created_at","desc")->paginate(10);
        $banksApi = Cache::remember('bancos', 24*60*60, function () {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://brasilapi.com.br/api/banks/v1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));
            $output = curl_exec($ch);
            curl_close($ch);
            $output = json_decode($output);
            $formated = [];
            foreach ($output as $bank) {
                $bank->code = str_pad($bank->code, 3, "0", STR_PAD_LEFT);
                if ($bank->code != "000") {
                    $formated[] = $bank;
                }
            }
            return $formated;
        });
        return view("financeiro.index", [
            "saques" => $saques,
            "banksApi" => $banksApi
        ]);
    }

    public function solicitar(Request $request) {
        $request->validate([
            "valor" => "required|numeric|min:30",
            "bank_account" => "required|numeric"
        ],
        [
            "valor.required" => "O valor é obrigatório!",
            "valor.numeric" => "O valor deve ser um número!",
            "valor.min" => "O valor mínimo é de R$ 30,00!",
            "bank_account.required" => "A chave PIX é obrigatória!"
        ]);
        $user = Auth::user();

        if ($user->saldoDisponivel < $request->valor) {
            return redirect()->route("financeiro.index")->withErrors(["O valor solicitado é maior que o seu saldo"]);
        }
        $account = BankAccount::find($request->bank_account)->where("user_id", $user->id)->first();
        if (!$account) {
            return redirect()->route("financeiro.index")->withErrors(["Conta bancária não encontrada"]);
        }
        $saque = new Saque();
        $saque->user_id = $user->id;
        $saque->valor = $request->valor - 6.90;
        $saque->bank_account_id = $account->id;
        $saque->created_at = Carbon::now("America/Sao_Paulo");
        $saque->status = 0;
        $saque->save();

        $user->saldoDisponivel -= $request->valor;
        $user->save();

        return redirect()->route("financeiro.index")->with("success", "Solicitação de saque realizada com sucesso");
    }


    public function cadastrar(Request $request) {
        $request->validate([
            "bankcode" => "required|numeric",
            "agency" => "required|numeric",
            "account" => "required|numeric",
            "account_digit" => "required|numeric|digits:1",
        ],
        [
            "bankcode.required" => "O código do banco é necessário!",
            "bankcode.numeric" => "O código do banco deve ser um número!",
            "agency.required" => "A agência é obrigatória!",
            "agency.numeric" => "A agência deve ser um número!",
            "agency.min" => "A agência deve ter no mínimo 3 dígitos!",
            "account.required" => "A conta é obrigatória!",
            "account.numeric" => "A conta deve ser um número!",
            "account.digits" => "A conta deve ter 5 dígitos!",
            "account_digit.required" => "O dígito da conta é obrigatório!",
            "account_digit.numeric" => "O dígito da conta deve ser um número!",
            "account_digit.digits" => "O dígito da conta deve ter 1 dígito!",
        ]);
        $user = Auth::user();
        $bank = new BankAccount();
        $bank->user_id = $user->id;
        $bank->bank_code = $request->bankcode;
        $bank->agency = $request->agency;
        $bank->account = $request->account;
        $bank->account_digit = $request->account_digit;

        try { 
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://brasilapi.com.br/api/banks/v1/".$request->bankcode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json'
            ));

            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response);
            if ($response) {
                $bank->bank_name = $response->name;
                $bank->save();
                return redirect()->route("financeiro.index")->with("success", "Conta bancária cadastrada com sucesso!");
            } else {
                return redirect()->route("financeiro.index")->withErrors(["O código do banco não é válido!"]);
            }
        } catch (\Exception $e) {
            return redirect()->route("financeiro.index")->withErrors(["Ocorreu um erro ao cadastrar a conta bancária!"]);
        }

    }

    public function excluir($id) {
        $user = Auth::user();
        $bank = BankAccount::where("user_id", $user->id)->where("id", $id)->first();
        if ($bank) {
            $bank->delete();
            return redirect()->route("financeiro.index")->with("success", "Conta bancária excluída com sucesso!");
        } else {
            return redirect()->route("financeiro.index")->withErrors(["Conta bancária não encontrada!"]);
        }
    }
}
