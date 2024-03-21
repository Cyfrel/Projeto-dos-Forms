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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('fonte');
            $table->string('cor');
            $table->string('pergunta');
            $table->string('tipo');
            $table->string('resposta');
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
