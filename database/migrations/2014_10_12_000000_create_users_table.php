<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->string('document')->unique();
            $table->string('telefone');
            $table->string('pix')->nullable();
            $table->float("saldoDisponivel", 8, 2)->default(0);
            $table->float("saldoPendente", 8, 2)->default(0);
            $table->integer("privilegio")->default(0);
            $table->boolean("ativo")->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
