<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Respostas;

class RespostasSeeder extends Seeder
{
    public function run(): void
    {
        $numeroForms = 200;
        $numeroRespostas = 10000;

        for ($i = 1; $i <= $numeroForms; $i++) {
            for ($y = 1; $y <= $numeroRespostas; $y++) {
                $id_usuario = $this->generateUniqueRandomNumeric(6);

                Respostas::create([
                    'id_forms' => $i,
                    'id_pergunta' => $i,
                    'id_usuario' => $id_usuario,
                    'resposta' => $this->generateRandomString(10),
                ]);
            }
        }

        $numeroForms2 = 1;
        $numeroRespostas2 = 100000;

        for ($i = 1; $i <= $numeroForms2; $i++) {
            for ($y = 1; $y <= $numeroRespostas2; $y++) {
                $id_usuario = $this->getExistingRandomUserId();

                Respostas::create([
                    'id_forms' => '201',
                    'id_pergunta' => '201',
                    'id_usuario' => $id_usuario,
                    'resposta' => $this->generateRandomString(10),
                ]);
            }
        }
    }

    private function getExistingRandomUserId()
    {
        // Recupera um ID de usuário aleatório existente na tabela de respostas
        $randomUser = Respostas::inRandomOrder()->first();

        return $randomUser->id_usuario ?? null; // Retorna o ID de usuário aleatório ou null se não houver registros
    }
    
    private function generateUniqueRandomNumeric($length)
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $randomId = $this->generateRandomNumeric($length);

            $exists = Respostas::where('id_usuario', $randomId)->exists();

            if (!$exists) {
                return $randomId;
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        return null;
    }

    private function generateRandomNumeric($length)
    {
        return rand(pow(10, $length - 1), pow(10, $length) - 1);
    }

    private function generateRandomString($length)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}
