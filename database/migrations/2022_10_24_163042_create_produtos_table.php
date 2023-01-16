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
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('descricao');
            $table->string("imagem");
            $table->float('preco', 8, 2);
            $table->string('plataforma');
            $table->float("taxaPercentual", 8, 2);
            $table->float("taxaFixa", 8, 2);
            $table->boolean("liberado")->default(false);
            $table->boolean("ativo")->default(true);
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
        Schema::dropIfExists('produtos');
    }
};
