<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrescricaoInternasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescricao_internas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('evolucao_paciente_id');
            $table->foreign('evolucao_paciente_id')->references('id')->on('evolucao_pacientes');
            $table->string('medicamento');
            $table->integer('quantidade');

            $table->boolean('ativo')->default(true);
            $table->integer('versao')->default(1);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prescricao_internas');
    }
}
