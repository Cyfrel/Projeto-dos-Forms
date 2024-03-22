<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsersFormsController extends Controller
{

    public function create(Request $request)
    {
        try {
            // Verifica se o nome do usuário foi enviado
            if (!$request->has('nome') || empty($request->input('nome'))) {
                return response()->json(['error' => 'Nome do usuário é obrigatório'], 400);
            }

            // Cria um novo usuário com o nome fornecido
            $novoUsuario = new Users();
            $novoUsuario->nome = $request->input('nome');
            $novoUsuario->save();

            // Gera um token para o novo usuário
            $token = Str::random(60);

            // Atualiza o token do usuário na tabela
            $novoUsuario->update(['remember_token' => $token]);

            // Retorna o token gerado como resposta
            return response()->json(['token' => $token], 201);
        } catch (\Exception $e) {
            // Trata erros
            \Log::error('Erro ao cadastrar usuário: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao cadastrar usuário'], 500);
        }
    }
    
    

    public function show(Request $request)
    {   
        try {
            $id = $request->query('id');
            $user = Users::findOrFail($id);
        
            return response()->json($user);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }
    }
}
