<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificadosTable extends Migration
{
    public function up()
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf')->nullable();
            $table->string('email')->nullable();
            $table->string('curso')->nullable();
            $table->string('carga_horaria')->nullable();
            $table->string('unidade')->nullable();
            $table->date('data_conclusao')->nullable();
            $table->timestamp('data_emissao')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('certificado_path')->nullable();
            $table->string('hash')->nullable();
            $table->unsignedBigInteger('emissao_certificados_arquivos_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificados');
    }
}
