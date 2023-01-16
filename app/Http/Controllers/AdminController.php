<?php

namespace App\Http\Controllers;

use App\Models\Afiliacao;
use App\Models\BankAccount;
use App\Models\Produto;
use App\Models\Saque;
use App\Models\User;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function index()
    {
        $lucros = Cache::remember('lucros', 30 * 60, function () {
            $vendaMes = Venda::where("created_at", ">", Carbon::now("America/Sao_Paulo")->format('Y-m-01 00:00:00'))->where("status", "=", "1")->get();
            $lucroDia = 0;
            $lucroMes = 0;
            foreach ($vendaMes as $venda) {
                $produto = Produto::find($venda->produto_id);
                if (Carbon::create($venda->created_at)->format('Y-m-d') == Carbon::now("America/Sao_Paulo")->format('Y-m-d')) {
                    if (!$venda->comTaxas) {
                        $lucroDia += $venda->valor;
                        $lucroDia -= ($venda->valor * $produto->taxaPercentual / 100) + $produto->taxaFixa;
                    } else {
                        $lucroDia += $venda->comTaxas;
                    }
                    $lucroDia -= $venda->comissao;
                }
                if (!$venda->comTaxas) {
                    $lucroMes += $venda->valor;
                    $lucroMes -= ($venda->valor * $produto->taxaPercentual / 100) + $produto->taxaFixa;
                } else {
                    $lucroMes += $venda->comTaxas;
                }
                $lucroMes -= $venda->comissao;
            }
            return [
                "lucroDia" => $lucroDia,
                "lucroMes" => $lucroMes
            ];
        });

        $lucroDia = $lucros["lucroDia"];
        $lucroMes = $lucros["lucroMes"];

        $vendas = Venda::orderBy("created_at", "desc")->paginate(30);
        foreach ($vendas as $venda) {
            $venda->produto = Produto::find($venda->produto_id)->nome;
            $venda->user = User::find($venda->user_id)->email;
        }

        $saldosDisponiveis = User::sum("saldoDisponivel");
        $saldosPendentes = User::sum("saldoPendente");

        $saldoAPagar = Saque::where("status", 0)->sum("valor");


        return view("admin.home.index", compact("lucroDia", "lucroMes", "saldoAPagar", "saldosDisponiveis", "saldosPendentes", "vendas"));
    }

    public function saque(Request $request, $id)
    {
        $saque = Saque::find($id);
        if ($request->method == "pago") {
            $ch = curl_init();
            $bank_account = BankAccount::find($saque->bank_account_id);
            $user = User::find($bank_account->user_id);
            if (!$bank_account) {
                return redirect()->back()->withErrors(["error" => "Conta bancária não encontrada"]);
            }
            curl_setopt($ch, CURLOPT_URL, "https://asaas.com/api/v3/transfers");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "access_token: " . env("ASAAS_TOKEN"),
                "Content-Type: application/json"
            ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "value" => $saque->valor,
                "bankAccount" => [
                    "bank" => [
                        "code" => $bank_account->bank_code
                    ],
                    "ownerName" => $user->nome,
                    "cpfCnpj" => $user->document,
                    "ownerBirthDate" => "1990-01-01",
                    "agency" => $bank_account->agency,
                    "account" => $bank_account->account,
                    "accountDigit" => $bank_account->account_digit,
                    "scheduledDate" => Carbon::now("America/Sao_Paulo")->format("Y-m-d"),
                    "bankAccountType" => "CONTA_CORRENTE"
                ],
                "operationType" => "TED"
            ]));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                return redirect()->back()->withErrors(["error" => "Erro ao enviar transferência"]);
            }
            curl_close($ch);
            $result = json_decode($result);
            if (isset($result->errors)) {
                return redirect()->back()->withErrors($result->errors);
            }

            $saque->status = 1;
            $saque->paid_at = Carbon::now("America/Sao_Paulo");
            $saque->save();
        } else if ($request->method == "recusar") {
            $saque->status = 2;
            $saque->save();
            $user = User::find($saque->user_id);
            $user->saldoDisponivel += $saque->valor;
            $user->save();
        } else {
            return redirect()->back()->withErrors(["error" => "Erro ao processar saque"]);
        }
        return redirect()->back()->with("success", ["Saque atualizado com sucesso"]);
    }


    public function ranking()
    {
        $ranking = Cache::remember("ranking", 30 * 60, function () {
            $ranking = [];
            foreach (User::all() as $user) {
                $metricas = Cache::remember('vendas_metricas_user' . $user->id, 30 * 60, function () use ($user) {
                    $vendasMesList = Venda::where('user_id', $user->id)->orWhereJsonContains('subseller', ["id" => $user->id])->where("status", "=", 1)->where("created_at", ">", Carbon::now("America/Sao_Paulo")->format('Y-m-01 00:00:00'))->get();
                    $vendasMes = 0;
                    $vendasDia = 0;

                    foreach ($vendasMesList as $venda) {
                        $comissao = $venda->comissao;
                        if ($venda->subseller) {
                            foreach ($venda->subseller as $subseller) {
                                if ($subseller['id'] == $user->id) {
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
                $user->vendasDia = $metricas["vendasDia"];
                $user->vendasMes = $metricas["vendasMes"];
                $ranking[] = $user;
            }
            usort($ranking, function ($a, $b) {
                return $b->vendasMes - $a->vendasMes;
            });
            return $ranking;
        });
        return view("admin.ranking", ["ranking" => $ranking]);
    }


    public function financeiro()
    {
        $saques = Saque::orderBy("status", "asc")->orderBy("created_at", "desc")->paginate(10)->through(function ($saque) {
            $saque->email = User::find($saque->user_id)->email;
            $saque->nome = User::find($saque->user_id)->nome;
            $saque->cpf = User::find($saque->user_id)->document;
            $saque->bank = BankAccount::find($saque->bank_account_id);

            if ($saque->bank == null) {
                $saque->bank = new BankAccount();
                $saque->bank->bank_code = "00";
                $saque->bank->agency = "N/A";
                $saque->bank->account = "N/A";
                $saque->bank->account_digit = "N/A";
                $saque->bank->bank_name = "Banco não encontrado";
            }

            return $saque;
        });
        return view("admin.financeiro.index", compact("saques"));
    }

    public function afiliacao()
    {
        $afiliacoes = Afiliacao::orderBy("created_at", "desc")->paginate(10)->through(function ($afiliacao) {
            $afiliacao->user = User::find($afiliacao->user_id);
            return $afiliacao;
        });

        $produtos = Produto::all();

        return view("admin.afiliacoes.index", compact("afiliacoes", "produtos"));
    }

    public function cadastraAfiliacao(Request $request)
    {
        $request->validate(
            [
                "email" => "required|email",
                "comissao" => "required|numeric",
                "link" => "required|string",
                "produto_id" => "required|numeric",
                "subseller" => "json|nullable"
            ],
            [
                "email.required" => "O campo email é obrigatório",
                "email.email" => "O campo email deve ser um email válido",
                "comissao.required" => "O campo comissão é obrigatório",
                "comissao.numeric" => "O campo comissão deve ser um número",
                "link.required" => "O campo link é obrigatório",
                "link.string" => "O campo link deve ser uma URL",
                "subsellers.json" => "O campo subsellers está inválido",
                "produto_id.required" => "O campo produto é obrigatório",
                "produto_id.numeric" => "O campo produto deve ser um número"
            ]
        );
        $afiliacao = new Afiliacao();
        $user = User::where("email", $request->email)->first();
        if ($user == null) {
            return redirect()->back()->withErrors(["O email informado não está cadastrado"]);
        }
        $afiliacao->user_id = $user->id;
        $afiliacao->comissao = $request->comissao;
        $afiliacao->link = $request->link;
        $afiliacao->produto_id = $request->produto_id;
        $afiliacao->plan_id = $request->plan_id;
        if ($request->subseller) {
            $afiliacao->subseller = $request->subseller;
        }
        $afiliacao->created_at = Carbon::now("America/Sao_Paulo");
        $afiliacao->save();
        return redirect()->back()->with("success", ["Afiliação cadastrada com sucesso"]);
    }
}
