<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    use HasFactory;

    // Tabela
    protected $table = 'forms';

    // Colunas
    //protected $fillable = ['titulo', 'data_registro', 'fonte', 'cor', 'pergunta', 'resposta', 'url_notificacao'];
    protected $fillable = [ 'titulo', 'fonte', 'cor', 'pergunta', 'tipo', 'resposta', 'url_notificacao'];

    
}
