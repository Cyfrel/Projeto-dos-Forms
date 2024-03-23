<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Users;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $numRecords = 2; // Altere para o número desejado de registros

        // Loop para criar e inserir os registros na tabela
        for ($i = 0; $i < $numRecords; $i++) {
            Users::create([
                'nome' => $this->generateRandomString(10), // Gera um nome aleatório de 10 caracteres
                'email' => $this->generateRandomEmail(), // Gera um email aleatório com o domínio example.com
                'remember_token' => $this->generateRandomStringTOKEN(60), // Gera um token aleatorio
                'limite_respostas' => '100',
            ]);
        }
        
    }

    private function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }


    private function generateRandomStringTOKEN($length = 60)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
    /**
     * Gera um email aleatório.
     *
     * @return string
     */
    private function generateRandomEmail()
    {
        return $this->generateRandomString(10) . '@example.com'; // Gera um email aleatório com o domínio example.com
    }
}
