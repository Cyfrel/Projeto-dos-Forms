<?php

namespace App\Http\Controllers;

use App\Models\Respostas;
use App\Models\Forms;
use App\Models\Users;
use App\Models\Perguntas;
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

    /* funcionando sem verificar se formulario existe
    public function create(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Criar uma nova instância da model Respostas com os dados recebidos
            $resposta = new Respostas();
            
            // Atribuir os valores do JSON aos campos da model
            $resposta->id_forms = $requestData['id_forms'] ?? null;
            $resposta->id_pergunta = $requestData['id_pergunta'] ?? null;
            $resposta->id_usuario = $requestData['id_usuario'] ?? null;
            $resposta->resposta = $requestData['resposta'] ?? null;

            // Verificar se o campo id_pergunta está presente e se a resposta não está vazia
            if (!isset($requestData['id_pergunta'])) {
                return response()->json(['error' => 'Certifique-se de incluir o id_pergunta.'], 400);
            }

            if (empty($requestData['resposta'])) {
                return response()->json(['error' => 'Certifique-se de incluir a resposta.'], 400);
            }

            if (empty($requestData['id_forms'])) {
                return response()->json(['error' => 'Certifique-se de incluir id_forms.'], 400);
            }

            if (empty($requestData['id_usuario'])) {
                return response()->json(['error' => 'Certifique-se de incluir id_usuario.'], 400);
            }

            // Salvar a nova instância no banco de dados
            $resposta->save();

            return response()->json($resposta, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao inserir a resposta. Por favor, tente novamente.'], 500); // 500 significa Internal Server Error
        }
    }*/


    public function create(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Verificar se estamos no primeiro dia do mês
            if (date('d') == 1) {
                // Atualizar o campo "limite_respostas" para "0" na tabela "forms"
                Forms::where('id', $requestData['id_forms'])->update(['limite_respostas' => 100]);
            }

            // Verificar se o usuário respondeu todas as perguntas do formulário
            $totalPerguntas = Perguntas::where('id_forms', $requestData['id_forms'])->count();
            $totalRespostasUsuario = Respostas::where('id_forms', $requestData['id_forms'])
                ->where('id_usuario', $requestData['id_usuario'])
                ->count();

            if ($totalRespostasUsuario >= $totalPerguntas) {
                return response()->json(['error' => 'O usuário já respondeu todas as perguntas deste formulário.'], 400);
            }

            // Verificar se o usuário respondeu todas as perguntas do formulário
            $totalRespostas = Respostas::where('id_forms', $requestData['id_forms'])
                ->count();

            // Verificar o limite de respostas permitido para o formulário
            $limiteRespostas = Forms::where('id', $requestData['id_forms'])->value('limite_respostas');

            if ($totalRespostas >= $limiteRespostas) {
                return response()->json(['error' => 'O número máximo de respostas para este formulário já foi atingido.'], 400);
            }


            // Verificar se o campo id_forms está presente e se ele existe na tabela forms
            if (!isset($requestData['id_forms'])) {
                return response()->json(['error' => 'Certifique-se de incluir o id_forms.'], 400);
            }

             // Verificar se o campo id_pergunta está presente e se a resposta não está vazia
             if (!isset($requestData['id_pergunta'])) {
                return response()->json(['error' => 'Certifique-se de incluir o id_pergunta.'], 400);
            }

            if (empty($requestData['resposta'])) {
                return response()->json(['error' => 'Certifique-se de incluir a resposta.'], 400);
            }

            if (empty($requestData['id_usuario'])) {
                return response()->json(['error' => 'Certifique-se de incluir id_usuario.'], 400);
            }

            $formExists = Forms::where('id', $requestData['id_forms'])->exists();
            if (!$formExists) {
                return response()->json(['error' => 'O id_forms fornecido não existe na tabela forms.'], 400);
            }

            $formExists = Users::where('id', $requestData['id_usuario'])->exists();
            if (!$formExists) {
                return response()->json(['error' => 'O id_usuario fornecido não existe na tabela Users.'], 400);
            }

            // Verificar se o campo id_pergunta existe na tabela perguntas
            $pergunta = Perguntas::where('id_forms', $requestData['id_forms'])->where('id', $requestData['id_pergunta'])->exists();
            if (!$pergunta) {
                return response()->json(['error' => 'Não existe pergunta ' . $requestData['id_pergunta'] . ' no formulário ' . $requestData['id_forms'] . '.'], 400);
            }

            // Verificar se o usuário já respondeu a essa pergunta
            $existingResponse = Respostas::where('id_forms', $requestData['id_forms'])
                ->where('id_pergunta', $requestData['id_pergunta'])
                ->where('id_usuario', $requestData['id_usuario'])
                ->exists();

            if ($existingResponse) {
                return response()->json(['error' => 'O usuário já respondeu esta pergunta.'], 400);
            }

            
            // Criar uma nova instância da model Respostas com os dados recebidos
            $resposta = new Respostas();
            
            // Atribuir os valores do JSON aos campos da model
            $resposta->id_forms = $requestData['id_forms'];
            $resposta->id_pergunta = $requestData['id_pergunta'] ?? null;
            $resposta->id_usuario = $requestData['id_usuario'] ?? null;
            $resposta->resposta = $requestData['resposta'] ?? null;

            // Salvar a nova instância no banco de dados
            $resposta->save();

            
            // Decrementar limite de respostas
            Forms::where('id', $requestData['id_forms'])->decrement('limite_respostas');
            

            return response()->json($resposta, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao inserir a resposta. Por favor, tente novamente.'], 500); // 500 significa Internal Server Error
        }
    }

    /* verifica se usuario existe
    public function create(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Verificar se o campo id_forms está presente e se ele existe na tabela forms
            if (!isset($requestData['id_forms'])) {
                return response()->json(['error' => 'Certifique-se de incluir o id_forms.'], 400);
            }

            $formExists = Forms::where('id', $requestData['id_forms'])->exists();
            if (!$formExists) {
                return response()->json(['error' => 'O id_forms fornecido não existe na tabela forms.'], 400);
            }

            $formExists = Users::where('id', $requestData['id_usuario'])->exists();
            if (!$formExists) {
                return response()->json(['error' => 'O id_usuario fornecido não existe na tabela Users.'], 400);
            }

            // Criar uma nova instância da model Respostas com os dados recebidos
            $resposta = new Respostas();
            
            // Atribuir os valores do JSON aos campos da model
            $resposta->id_forms = $requestData['id_forms'];
            $resposta->id_pergunta = $requestData['id_pergunta'] ?? null;
            $resposta->id_usuario = $requestData['id_usuario'] ?? null;
            $resposta->resposta = $requestData['resposta'] ?? null;

            // Verificar se o campo id_pergunta está presente e se a resposta não está vazia
            if (!isset($requestData['id_pergunta'])) {
                return response()->json(['error' => 'Certifique-se de incluir o id_pergunta.'], 400);
            }

            if (empty($requestData['resposta'])) {
                return response()->json(['error' => 'Certifique-se de incluir a resposta.'], 400);
            }

            if (empty($requestData['id_usuario'])) {
                return response()->json(['error' => 'Certifique-se de incluir id_usuario.'], 400);
            }

            // Salvar a nova instância no banco de dados
            $resposta->save();

            return response()->json($resposta, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao inserir a resposta. Por favor, tente novamente.'], 500); // 500 significa Internal Server Error
        }
    }*/

    /* verifica se usuario ja respondeu a pergunta
    public function create(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Verificar se os campos obrigatórios estão presentes
            if (!isset($requestData['id_forms'], $requestData['id_pergunta'], $requestData['id_usuario'], $requestData['resposta'])) {
                return response()->json(['error' => 'Certifique-se de incluir todos os campos obrigatórios: id_forms, id_pergunta, id_usuario e resposta.'], 400);
            }

            // Verificar se o campo id_forms existe na tabela forms
            $formExists = Forms::where('id', $requestData['id_forms'])->exists();
            if (!$formExists) {
                return response()->json(['error' => 'O id_forms fornecido não existe na tabela forms.'], 400);
            }

            // Verificar se o campo id_usuario existe na tabela users
            $userExists = Users::where('id', $requestData['id_usuario'])->exists();
            if (!$userExists) {
                return response()->json(['error' => 'O id_usuario fornecido não existe na tabela Users.'], 400);
            }

            // Verificar se o usuário já respondeu a essa pergunta
            $existingResponse = Respostas::where('id_forms', $requestData['id_forms'])
                ->where('id_pergunta', $requestData['id_pergunta'])
                ->where('id_usuario', $requestData['id_usuario'])
                ->exists();

            if ($existingResponse) {
                return response()->json(['error' => 'O usuário já respondeu esta pergunta.'], 400);
            }

            // Criar uma nova instância da model Respostas com os dados recebidos
            $resposta = new Respostas();
            
            // Atribuir os valores do JSON aos campos da model
            $resposta->id_forms = $requestData['id_forms'];
            $resposta->id_pergunta = $requestData['id_pergunta'];
            $resposta->id_usuario = $requestData['id_usuario'];
            $resposta->resposta = $requestData['resposta'];

            // Salvar a nova instância no banco de dados
            $resposta->save();

            return response()->json($resposta, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao inserir a resposta. Por favor, tente novamente.'], 500); // 500 significa Internal Server Error
        }
    }*/




}
