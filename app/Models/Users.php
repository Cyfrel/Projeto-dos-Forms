<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;

    // Tabela
    protected $table = 'users';

    // Colunas
    //protected $fillable = ['titulo', 'data_registro', 'fonte', 'cor', 'pergunta', 'resposta', 'url_notificacao'];
    protected $fillable = [ 'name', 'email', 'password', 'remember_token', 'tipo', 'resposta', 'url_notificacao'];

    
}
