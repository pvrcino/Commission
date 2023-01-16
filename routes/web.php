<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AfiliacaoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\SaqueController;
use App\Http\Controllers\VendasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post("/whk/pepper", VendasController::class . "@pepper")->name("whk.pepper");


Route::group(["middleware" => "guest"], function () {
    Route::get("/login", [AuthController::class, "login"])->name("auth.login");
    Route::post("/login", [AuthController::class, "loginRequest"])->name("auth.login.request");
    Route::get("/register", [AuthController::class, "register"])->name("auth.register");
    Route::post("/register", [AuthController::class, "registerRequest"])->name("auth.register.request");
});

Route::group(["middleware" => "auth"], function () {
    Route::get("/", [HomeController::class, "index"])->name("home.index");
    Route::get("/logout", [AuthController::class, "logout"])->name("auth.logout");
    Route::get("/produtos", [ProdutosController::class, "index"])->name("produtos.index");
    Route::get("/vendas", [VendasController::class, "index"])->name("vendas.index");
    Route::get("/financeiro", [SaqueController::class, "index"])->name("financeiro.index");
    Route::post("/contas", [SaqueController::class, "cadastrar"])->name("financeiro.cadastrarConta");
    Route::delete("/contas/{id}", [SaqueController::class, "excluir"])->name("financeiro.excluirConta");
    Route::post("/solicitar", [SaqueController::class, "solicitar"])->name("financeiro.solicitar");
});

Route::group(['prefix' => 'whkac'], function () {
    Route::post("/abmex", VendasController::class . "@abmex")->name("whk.abmex");
    Route::post("/pepper", VendasController::class . "@pepper")->name("whk.pepper");
});

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    Route::get("/", [AdminController::class, "index"])->name("admin.home.index");
    Route::get("/financeiro", [AdminController::class, "financeiro"])->name("admin.financeiro.index");
    Route::post("/financeiro/{id}", [AdminController::class, "saque"])->name("admin.financeiro.saque");
    Route::get("/afiliacao", [AdminController::class, "afiliacao"])->name("admin.afiliacao.index");
    Route::post("/afiliacao/cadastrar", [AdminController::class, "cadastraAfiliacao"])->name("admin.afiliacao.cadastrar");
});

Route::get("/af/{id}", [AfiliacaoController::class, "redirect"])->name("afiliacao.redirect");