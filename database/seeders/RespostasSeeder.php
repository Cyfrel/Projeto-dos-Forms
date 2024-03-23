<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Respostas;

class RespostasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $numeroForms = 200; // Altere para o número desejado de registros
        $numeroRespostas = 10000;
        // Loop para criar e inserir os registros na tabela
        for ($i = 1; $i <= $numeroForms; $i++) {
            for ($y = 1; $y <= $numeroRespostas; $y++) {
                Respostas::create([
                    'id_forms' => $i, // Gera um nome aleatório de 10 caracteres
                    'id_pergunta' => $i,
                    'id_usuario' => $this->generateRandomNumeric(4), 
                    'resposta' => $this->generateRandomString(10), // Gera um nome aleatório de 10 caracteres
                ]);
            }
        }

        $numeroForms2 = 1; // Altere para o número desejado de registros
        $numeroRespostas2 = 100000;
        // Loop para criar e inserir os registros na tabela
        for ($i = 1; $i <= $numeroForms2; $i++) {
            for ($y = 1; $y <= $numeroRespostas2; $y++) {
                Respostas::create([
                    'id_forms' => '201', // Gera um nome aleatório de 10 caracteres
                    'id_pergunta' => '201',
                    'id_usuario' => $this->generateRandomNumeric(4), 
                    'resposta' => $this->generateRandomString(10), // Gera um nome aleatório de 10 caracteres
                ]);
            }
        }
    }


    private function generateRandomNumeric($length)
    {
        return rand(pow(10, $length-1), pow(10, $length)-1); // Gera um número aleatório de $length dígitos
    }

    private function generateRandomString($length)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
}
