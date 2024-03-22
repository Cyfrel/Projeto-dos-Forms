<?php

namespace App\Http\Controllers;

use App\Models\Forms;
use App\Models\Perguntas;
use App\Models\Respostas;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FormsController extends Controller
{   


    public function create(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            $token = $request->header('Authorization');

            // Busca o usuário com base no token
            $user = Users::where('remember_token', $token)->first();

            $id_usuario = $user->id;

            // Criar o formulário usando o modelo Forms
            $forms = Forms::create([
                'titulo' => $requestData['titulo'],
                'fonte' => $requestData['fonte'],
                'cor' => $requestData['cor'],
                'id_usuario' => $id_usuario,
                'limite_respostas' => '100',
                'url_notificacao' => $requestData['url_notificacao'],
            ]);

            return response()->json($forms, 201); // 201 significa Created
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao criar o formulário'], 500); // 500 significa Internal Server Error
        }
    }
    
    public function create_perguntas(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();

            // Criar o formulário usando o modelo Perguntas
            $pergunta = new Perguntas();
            $pergunta->id_forms = $requestData['id_forms'] ?? null;
            $pergunta->tipo_resposta = $requestData['tipo_resposta'];


            $token = $request->header('Authorization');

            // Busca o usuário com base no token
            $user = Users::where('remember_token', $token)->first();

            if ($user) {
                // Verifica se o usuário possui o formulário com o ID fornecido
                $form = Forms::where('id', $requestData['id_forms'])
                            ->where('id_usuario', $user->id)
                            ->first();

                if ($form) {
                    // Busca as respostas apenas se o usuário possuir o formulário com o ID fornecido
                    if (!($requestData['tipo_resposta'] == 1 || $requestData['tipo_resposta'] == 2 || $requestData['tipo_resposta'] == 3)) {
                        return response()->json(['error' => 'Por favor insira um tipo_resposta valido. (1-simples  2-composta  3-estruturada).'], 400);
                    }
                    
        
                    $formExists = Forms::where('id', $requestData['id_forms'])->exists();
                    if (!$formExists) {
                        return response()->json(['error' => 'O id_forms fornecido não existe na tabela forms.'], 400);
                    }
                    // Salvar a pergunta ou perguntas e respostas de acordo com o tipo de resposta
                    switch ($requestData['tipo_resposta']) {
                        case 1:
                            // Verificar se existem pergunta e resposta
                            if (!isset($requestData['pergunta']) || !isset($requestData['resposta'])) {
                                if (!isset($requestData['pergunta'])) {
                                    return response()->json(['error' => 'Insira Pergunta'], 400);
                                }
        
                            }
                            // Salvar apenas uma resposta
                            $pergunta->pergunta = $requestData['pergunta'];
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
                } else {

                    return response()->json(['error' => 'Usuário não criou o formulario inserido'], 404);
                }
            }
            
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao criar o formulário'], 500); // 500 significa Internal Server Error
        }
    }
   

    public function show(Request $request)
    {   
        try {
            $id = $request->input('id');
            $filtro_respostas = $request->input('filtro respostas');


            // Carrega o formulário com as informações de título
            $form = Forms::findOrFail($id);



            $token = $request->header('Authorization');

            // Busca o usuário com base no token
            $user = Users::where('remember_token', $token)->first();

            if ($user) {
                // Verifica se o usuário possui o formulário com o ID fornecido
                $form = Forms::where('id', $id)
                            ->where('id_usuario', $user->id)
                            ->first();

                if ($form) {
                    // Busca as respostas apenas se o usuário possuir o formulário com o ID fornecido
                    // Carrega as perguntas relacionadas ao formulário
                    $perguntas = Perguntas::where('id_forms', $id)->get();
                    $numPerguntas = count($perguntas);
                    // Carrega as respostas relacionadas ao formulário
                    $respostas = Respostas::where('id_forms', $id)->get();

                    // Mapeia as perguntas e suas respostas correspondentes

                    
                       // Inicializa um array para armazenar o número de respostas por usuário
                    $respostasPorUsuario = [];

                    // Itera sobre as respostas para contar quantas respostas cada usuário deu
                    foreach ($respostas as $resposta) {
                        $idUsuario = $resposta->id_usuario;

                        // Verifica se o usuário já está no array de respostas por usuário
                        if (array_key_exists($idUsuario, $respostasPorUsuario)) {
                            // Se o usuário já existe, incrementa o contador de respostas
                            $respostasPorUsuario[$idUsuario]++;
                        } else {
                            // Se o usuário não existe, inicializa o contador de respostas para 1
                            $respostasPorUsuario[$idUsuario] = 1;
                        }
                    }

                    // Mapeia as perguntas e suas respostas correspondentes, incluindo o contador de respostas por usuário
                    $perguntasComRespostas = $perguntas->map(function ($pergunta) use ($respostas, $respostasPorUsuario) {
                        $respostasPergunta = $respostas->where('id_pergunta', $pergunta->id)->map(function ($resposta) use ($respostasPorUsuario) {
                            return [
                                'id_usuario' => $resposta->id_usuario,
                                'resposta' => $resposta->resposta,
                            ];
                        })->values()->toArray();

                        // Inclui o contador de respostas por usuário
                        $contadorRespostasPorUsuario = [];
                        foreach ($respostasPorUsuario as $idUsuario => $numRespostas) {
                            $contadorRespostasPorUsuario[] = [
                                'id_usuario' => $idUsuario,
                                'quantidade_respostas' => $numRespostas,
                            ];
                        }

                        return [
                            'id' => $pergunta->id,
                            'pergunta' => $pergunta->pergunta,
                            'respostas' => $respostasPergunta,
                            'contador_respostas_por_usuario' => $contadorRespostasPorUsuario,
                        ];
                    });

                    // Inclui o número total de perguntas na resposta JSON
                    return response()->json([
                        'form' => [
                            'id' => $form->id,
                            'titulo' => $form->titulo,
                            'perguntas' => $perguntasComRespostas,
                            'quantidade_de_perguntas' => $numPerguntas,
                            'filtro_respostsas' => $filtro_respostas,
                        ],
                    ]);

                    
                } else {

                    return response()->json(['error' => 'Usuário não criou o formulario inserido'], 404);
                }
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Formulário não encontrado'], 404);
        }
    }

    






    public function list(Request $request)
    {   
        try {
            $token = $request->header('Authorization');

            if(!$token) {
                return response()->json(['error' => 'Token de autorização não fornecido'], 401);
            }

            // Busca o usuário com base no token
            $user = Users::where('remember_token', $token)->first();

            if (!$user) {
                return response()->json(['error' => 'Usuário não autenticado'], 401);
            }

            // Busca todos os formulários do usuário logado
            $forms = Forms::where('id_usuario', $user->id)->get();
            
            if ($forms->isEmpty()) {
                return response()->json(['error' => 'Ainda não existe nenhum Formulário cadastrado para o usuário logado'], 404);
            }

            // Aqui você pode fazer o que precisar com os formulários encontrados, como retorná-los em JSON

            // Busca os IDs dos formulários do usuário logado
            $formIds = $forms->pluck('id');

            // Busca as respostas relacionadas aos formulários encontrados e agrupa pelo id_forms e conta as respostas para cada formulário
            $respostas = Respostas::whereIn('id_forms', $formIds)->select('id_forms', \DB::raw('COUNT(*) as count'))->groupBy('id_forms')->get();

            // Cria um array contendo os dados dos formulários e a quantidade de respostas para cada formulário
            $data = [];

            foreach ($forms as $form) {
                $contador_respostas = $respostas->firstWhere('id_forms', $form->id);
                $count = $contador_respostas ? $contador_respostas->count : 0;

                $data[] = [
                    'form' => $form,
                    'contador_de_respostas' => $count
                ];
            }

            // Retorna os dados como JSON
            return response()->json($data);
            
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ao listar formulários: ' . $exception->getMessage()], 500);
        }
    }


}
