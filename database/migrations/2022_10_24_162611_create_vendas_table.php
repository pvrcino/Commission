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
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("user_id")->unsigned();
            $table->bigInteger("afiliacao_id")->unsigned();
            $table->bigInteger("produto_id")->unsigned();
            $table->string("transaction_code")->index();
            $table->string("comprador");
            $table->float("valor", 8, 2);
            $table->float("comissao", 8, 2);
            $table->integer("status")->index();
            $table->integer("payment_type")->index();
            $table->timestamp("created_at")->index();
            $table->timestamp("paid_at")->nullable();
            $table->boolean("addSaldo")->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendas');
    }
};
