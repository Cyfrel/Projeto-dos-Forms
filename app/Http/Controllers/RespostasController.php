<?php

namespace App\Http\Controllers;

use App\Models\Respostas;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RespostasController extends Controller
{
    public function list(Request $request)
    {   
        try {
            $id_form = $request->query('id_forms');
            $respostas = Respostas::where('id_forms', $id_form)->get();

            if ($respostas->isEmpty()) {
                return response()->json(['error' => 'Nenhuma resposta encontrada para o ID de formulário fornecido'], 404);
            }

            return response()->json($respostas);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Nenhuma resposta encontrada para o ID de formulário fornecido'], 404);
        }
    }


    public function store(Request $request)
    {
        try {
            // Cadastrar no banco o formulário
            $forms = Respostas::create($request->all());

            return response()->json($forms, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao criar a resposta'], 500); // 500 significa Internal Server Error
        }
    }
}
