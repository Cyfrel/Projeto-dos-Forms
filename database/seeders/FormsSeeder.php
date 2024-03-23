<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Forms;
use App\Models\Perguntas;

class FormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

        //GERAR 200 FORMS PARA UM USER
        $numRecords = 200; // Altere para o número desejado de registros

        // Loop para criar e inserir os registros na tabela
        for ($i = 0; $i < $numRecords; $i++) {
            Forms::create([
                'titulo' => $this->generateRandomString(10), // Gera um nome aleatório de 10 caracteres
                'fonte' => 'times new roman',
                'cor' => 'Roxo', 
                'url_notificacao' => $this->generateRandomString(10), // Gera um nome aleatório de 10 caracteres
                'id_usuario' => '1',
            ]);
        }

        //GERAR 1 PERGUNTA PARA 200 FORMS
        $numRecordsPergunta = 200; // Altere para o número desejado de registros

        // Loop para criar e inserir os registros na tabela
        for ($i = 1; $i <= $numRecordsPergunta; $i++) {
            Perguntas::create([
                'id_forms' => $i, // Gera um nome aleatório de 10 caracteres
                'tipo_resposta' => '1',
                'resposta' => ' ', 
                'pergunta' => $this->generateRandomString(10).'?', // Gera um nome aleatório de 10 caracteres
            ]);
        }


        //GERAR 1 FORM PARA UM USER
        $numRecords = 1; // Altere para o número desejado de registros

        for ($i = 0; $i < $numRecords; $i++) {
            Forms::create([
                'titulo' => $this->generateRandomString(10), // Gera um nome aleatório de 10 caracteres
                'fonte' => 'times new roman', // Gera um nome aleatório de 10 caracteres
                'cor' => 'Roxo', // Gera um nome aleatório de 10 caracteres
                'url_notificacao' => $this->generateRandomString(10), // Gera um nome aleatório de 10 caracteres
                'id_usuario' => '2', // Gera um nome aleatório de 10 caracteres
            ]);
        }


        //GERAR 1 PERGUNTA PARA UM FORM
        $numRecordsPergunta = 1; // Altere para o número desejado de registros

        // Loop para criar e inserir os registros na tabela
        for ($i = 0; $i < $numRecordsPergunta; $i++) {
            Perguntas::create([
                'id_forms' => '201', // Gera um nome aleatório de 10 caracteres
                'tipo_resposta' => '1',
                'resposta' => ' ', 
                'pergunta' => $this->generateRandomString(10).'?', // Gera um nome aleatório de 10 caracteres
            ]);
        }
            
    }

    private function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

}
