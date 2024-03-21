<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respostas extends Model
{
    use HasFactory;



    // Tabela
    protected $table = 'respostas';

    // Colunas
    protected $fillable = [ 'id_forms', 'id_pergunta', 'id_usuario', 'resposta'];


}
