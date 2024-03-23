<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Models\Respostas;
use App\Models\Forms;
use App\Models\Users;
use App\Models\Perguntas;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mail\ContactMailable;
use Illuminate\Support\Facades\Mail;

class RespostasController extends Controller
{
    public function list(Request $request)
    {   
        try {
            $id_form = $request->json()->get('id_forms');

            $token = $request->header('Authorization');

            // Busca o usuário com base no token
            $user = Users::where('remember_token', $token)->first();

            if ($user) {
                // Verifica se o usuário possui o formulário com o ID fornecido
                $form = Forms::where('id', $id_form)
                            ->where('id_usuario', $user->id)
                            ->first();

                if ($form) {
                    // Busca as respostas apenas se o usuário possuir o formulário com o ID fornecido
                    $respostas = Respostas::where('id_forms', $id_form)->get();
                } else {

                    return response()->json(['error' => 'Usuário não criou o formulario inserido'], 404);
                }
            }

            if ($respostas->isEmpty()) {
                return response()->json(['error' => 'Nenhuma resposta encontrada para o ID de formulário fornecido'], 404);
            }

            return response()->json($respostas);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Nenhuma resposta encontrada para o ID de formulário fornecido'], 404);
        }
    }


    public function create(Request $request)
    {
        try {
            // Analisar o JSON recebido
            $requestData = $request->json()->all();


            if (empty($requestData['id_forms']) || !isset($requestData['id_forms'])) {
                return response()->json(['error' => 'Favor prencheer id_forms.'], 400);
            }
            
            // Busca o registro do formulário pelo ID
            $form = Forms::findOrFail($requestData['id_forms']);

            $token = $request->header('Authorization');

            $id_usuario_criador = $form->id_usuario;
            
            if (empty($form) || !isset($form)) {
                return response()->json(['error' => 'Formulário não encontrado.'], 400);
            }
            
            // Busca o usuário com base no token
            $user = Users::where('remember_token', $token)->first();
            
            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado.'], 400);
            }
            

            
            // Verifica se o usuário é o criador do formulário
            if ($form->id_usuario != $user->id) {
                return response()->json(['error' => 'Você não tem permissão para acessar este formulário.'], 403);
            }

            // Verificar se estamos no primeiro dia do mês
            if (date('d') == 1) {
                // Atualizar o campo "limite_respostas" para "0" na tabela "forms"

                $form = Forms::find($requestData['id_forms']);
                Users::where('id', $user->id)->update(['limite_respostas' => 100]);
            }

            // Verificar se o usuário respondeu todas as perguntas do formulário
            $totalPerguntas = Perguntas::where('id_forms', $requestData['id_forms'])->count();
            $totalRespostasUsuario = Respostas::where('id_forms', $requestData['id_forms'])
                ->where('id_usuario', $user->id)
                ->count();

            if ($totalRespostasUsuario >= $totalPerguntas) {

                if ($user->email) {

                    // Envie o webhook aqui
                    $httpClient = new \GuzzleHttp\Client();

                    $totalRespostasUsuariosend = Respostas::where('id_forms', $requestData['id_forms'])
                    ->where('id_usuario', $user->id)
                    ->get();

                    $respostasArray = $totalRespostasUsuariosend->toArray();

                    $response = $httpClient->post($form->url_notificacao, [
                        'json' => [
                            'user_id' => $user->id,
                            'form_id' => $requestData['id_forms'],
                            'respostas' => $respostasArray,
                            // Outros dados que você deseja enviar no webhook, se houver
                        ]
                    ]);
                    
                    // Verifica se o webhook foi enviado com sucesso (código de status HTTP 200)
                    if ($response->getStatusCode() === 200) {
                        // Registro de log ou qualquer outra ação que você deseja tomar se o webhook for enviado com sucesso
                        Log::info('Webhook enviado com sucesso para o usuário ' . $form->id_usuario);
                    } else {
                        // Registro de log ou tratamento de erro se o webhook não for enviado com sucesso
                        Log::error('Falha ao enviar o webhook para o usuário ' . $form->id_usuario);
                    }



                    

                    $send = Mail::to(users: $user->email, name: $user->nome)->send(mailable: new ContactMailable(data: [
                        'fromName' => $user->id,
                        'fromEmail' => 'emailderecompensas001@gmail.com',
                        'subject' => 'Formulário respondido',
                        'message' => 'Usuario: '.$user->id.' Respondeu o seu formulario:'.$requestData['id_forms'],
                    ]));
                }else{
                    return response()->json(['error' => 'O dono do form não possui email cadastrado para receber emails.'], 400);
                }

                return response()->json(['error' => 'O usuário já respondeu todas as perguntas deste formulário.'], 400);
            }

            // Verificar se o usuário respondeu todas as perguntas do formulário
            $totalRespostas = Respostas::where('id_forms', $requestData['id_forms'])
                ->count();

            // Verificar o limite de respostas permitido para o formulário
            $limiteRespostas = Users::where('id', $id_usuario_criador)->value('limite_respostas');

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

            if (empty($user->id)) {
                return response()->json(['error' => 'Certifique-se de incluir id_usuario.'], 400);
            }

            $formExists = Forms::where('id', $requestData['id_forms'])->exists();
            if (!$formExists) {
                return response()->json(['error' => 'O id_forms fornecido não existe na tabela forms.'], 400);
            }

            $formExists = Users::where('id', $user->id)->exists();
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
                ->where('id_usuario', $user->id)
                ->exists();

            if ($existingResponse) {
                return response()->json(['error' => 'O usuário já respondeu esta pergunta.'], 400);
            }

            
            // Criar uma nova instância da model Respostas com os dados recebidos
            $resposta = new Respostas();
            
            // Atribuir os valores do JSON aos campos da model
            $resposta->id_forms = $requestData['id_forms'];
            $resposta->id_pergunta = $requestData['id_pergunta'] ?? null;
            $resposta->id_usuario = $user->id ?? null;
            $resposta->resposta = $requestData['resposta'] ?? null;

            // Salvar a nova instância no banco de dados
            $resposta->save();

            
            // Decrementar limite de respostas
            Users::where('id', $id_usuario_criador)->decrement('limite_respostas');
            

            return response()->json($resposta, 201); // 201 significa Created
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            // Tratar o erro quando o formulário não for encontrado
            return response()->json(['error' => 'O formulário com o ID ' . $requestData['id_forms'] . ' não foi encontrado.'], 404); // 404 significa Not Found
        }
    }


}
