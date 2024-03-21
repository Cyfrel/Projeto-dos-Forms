<?php

namespace App\Http\Controllers;

use App\Models\Forms;
use App\Models\Perguntas;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FormsController extends Controller
{   


    /*
    public function create(Request $request)
    {
        try {
            // Cadastrar no banco o formulário
            $forms = Forms::create($request->all());

            return response()->json($forms, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao criar o formulário'], 500); // 500 significa Internal Server Error
        }
    }
    */

    public function create(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Criar o formulário usando o modelo Forms
            $forms = Forms::create([
                'titulo' => $requestData['titulo'],
                'fonte' => $requestData['fonte'],
                'cor' => $requestData['cor'],
                'pergunta' => $requestData['pergunta'],
                'tipo' => $requestData['tipo'],
                'resposta' => $requestData['resposta'],
                'url_notificacao' => $requestData['url_notificacao'],
            ]);

            return response()->json($forms, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao criar o formulário'], 500); // 500 significa Internal Server Error
        }
    }

    /*Funcionou salvar varias respostas

    public function create_perguntas(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Criar o formulário usando o modelo Perguntas
            $pergunta = new Perguntas();
            $pergunta->id_forms = $requestData['id_forms'] ?? null;
            $pergunta->tipo_resposta = $requestData['tipo_resposta'];
            $pergunta->pergunta = $requestData['pergunta'];
            
            // Concatenar todas as respostas no campo 'resposta' separadas por vírgula
            $respostas = [];
            foreach ($requestData as $key => $value) {
                if (strpos($key, 'resposta') === 0) { // Verificar se a chave começa com "resposta"
                    $respostas[] = $value;
                }
            }
            $pergunta->resposta = implode(',', $respostas);

            // Salvar a pergunta
            $pergunta->save();

            return response()->json(['pergunta' => $pergunta], 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao criar o formulário'], 500); // 500 significa Internal Server Error
        }
    }
    */

    public function create_perguntas(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Criar o formulário usando o modelo Perguntas
            $pergunta = new Perguntas();
            $pergunta->id_forms = $requestData['id_forms'] ?? null;
            $pergunta->tipo_resposta = $requestData['tipo_resposta'];
            
            // Salvar a pergunta ou perguntas e respostas de acordo com o tipo de resposta
            switch ($requestData['tipo_resposta']) {
                case 1:
                    // Verificar se existem pergunta e resposta
                    if (!isset($requestData['pergunta']) || !isset($requestData['resposta'])) {
                        if (!isset($requestData['pergunta'])) {
                            return response()->json(['error' => 'Insira Pergunta'], 400);
                        }
                        if (!isset($requestData['resposta'])) {
                            return response()->json(['error' => 'Insira Resposta'], 400);
                        }
                    }
                    // Salvar apenas uma resposta
                    $pergunta->pergunta = $requestData['pergunta'];
                    $pergunta->resposta = $requestData['resposta'];
                    $pergunta->save();
                    break;                

                case 2:
                    // Verificar se existe pergunta
                    if (!isset($requestData['pergunta'])) {
                        return response()->json(['error' => 'Insira uma pergunta'], 400);
                    }
                
                    // Contar o número de respostas presentes
                    $respostasCount = 0;
                    foreach ($requestData as $key => $value) {
                        if (strpos($key, 'resposta') === 0 && !empty($value)) {
                            $respostasCount++;
                        }
                    }
                
                    // Verificar se pelo menos duas respostas foram fornecidas
                    if ($respostasCount < 2) {
                        return response()->json(['error' => 'Pelo menos duas respostas são necessárias'], 400);
                    }
                
                    // Salvar uma pergunta e várias respostas
                    $respostas = array_filter($requestData, function ($key) {
                        return strpos($key, 'resposta') === 0;
                    }, ARRAY_FILTER_USE_KEY);
                
                    $pergunta->pergunta = $requestData['pergunta'];
                    $pergunta->resposta = implode(',', $respostas);
                    $pergunta->save();
                    break;
                    
                case 3:
                    // Verificar se existem perguntas e se há pelo menos duas
                    $perguntasCount = 0;
                    foreach ($requestData as $key => $value) {
                        if (strpos($key, 'pergunta') === 0) {
                            $perguntasCount++;
                        }
                    }
                    if ($perguntasCount < 2) {
                        return response()->json(['error' => 'Pelo menos duas perguntas são necessárias'], 400);
                    }
                    // Salvar várias perguntas e nenhuma resposta
                    $perguntas = [];
                    foreach ($requestData as $key => $value) {
                        if (strpos($key, 'pergunta') === 0) {
                            $perguntas[] = $value;
                        }
                    }
                    $pergunta->pergunta = implode(',', $perguntas);
                    $pergunta->save();
                    break;
                    
                    
                    
                default:
                    return response()->json(['error' => 'Tipo de resposta inválido'], 400); // 400 significa Bad Request
            }

            return response()->json(['pergunta' => $pergunta], 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao criar o formulário'], 500); // 500 significa Internal Server Error
        }
    }






    public function show(Request $request)
    {   
        try {
            $id = $request->query('id');
            $form = Forms::findOrFail($id);

            return response()->json($form);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Formulário não encontrado'], 404);
        }
    }

    public function list()
    {   
        try {
            $forms = Forms::orderBy('created_at')->get();

            if ($forms->isEmpty()) {
                return response()->json(['error' => 'Ainda não existe nenhum Formulário cadastrado'], 404);
            }

            //return view('forms.index');
            return response()->json($forms);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao listar formulários'], 500);
        }
    }
}
