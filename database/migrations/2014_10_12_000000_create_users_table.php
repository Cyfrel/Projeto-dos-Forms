<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('limite_respostas')->nullable();
            $table->rememberToken()->nullable();
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('fonte');
            $table->string('cor');
            $table->string('id_usuario');
            $table->string('url_notificacao');
            $table->timestamps();
        });

        Schema::create('respostas', function (Blueprint $table) {
            $table->id();
            $table->string('id_forms');
            $table->string('id_pergunta');
            $table->string('id_usuario');
            $table->string('resposta');
            $table->timestamps();
        });

        Schema::create('perguntas', function (Blueprint $table) {
            $table->id();
            $table->string('id_forms');
            $table->string('tipo_resposta');
            $table->string('pergunta');
            $table->string('resposta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
