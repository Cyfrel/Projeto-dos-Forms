# Projeto dos Formulários

Esta api tem como objetivo gerenciar o backend de uma ferramenta de gerenciamento de formulários personalizados online.

## Pré-requisitos

- PHP 
- Banco de dados MySQL

## Instalação

1. Clone o repositório:
```
git clone https://github.com/Cyfrel/Projeto-dos-Forms/tree/main
```
2. Configure seu banco de dados no arquivo .env:
```
DB_CONNECTION=mysql
DB_HOST=seu-host
DB_PORT=sua-porta
DB_DATABASE=seu-banco-de-dados
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
```
3.Execute as migrações do banco de dados para criar as tabelas necessárias:
```
php artisan migrate
```
4. Inicie o servidor embutido do Laravel:
```
php artisan serve
```
## Funcionalidades Principais:

1. Criação de Formulários: Os usuários podem criar formulários personalizados, especificando título, data de criação e atualização, estilo visual e perguntas a serem respondidas.

2. Listagem de Formulários: Os usuários têm acesso a uma lista de todos os formulários que criaram, juntamente com o total de pessoas que responderam a pelo menos uma pergunta em cada formulário.

3. Recebimento e Armazenamento de Respostas: O sistema é capaz de receber e armazenar as respostas dos formulários, incluindo o gerenciamento de respostas incompletas.

4. Visualização de Respostas: Os usuários podem visualizar todas as respostas de um formulário de forma amigável para o frontend, com opção de filtrar os resultados para listar todas as pessoas ou apenas aquelas que responderam todas as perguntas do formulário.

5. Notificação de Respostas: Quando um formulário é respondido por completo, o proprietário do formulário recebe um e-mail de notificação e um webhook é enviado para uma URL específica contendo as perguntas e respostas da pessoa que preencheu o formulário.

6. Controle de Consumo: Implementação de um limite de respostas por mês para cada usuário, com um limite compartilhado entre todos os formulários do usuário. Após atingir o limite, novas respostas não são aceitas até o próximo mês.

7. Segurança de acesso: os usuários só possuem permissão para acessar as informações relacionadas aos seus formulários cadastrados e a sua conta.


## Arquitetura e Escalabilidade:

1. Armazenamento de Dados: O sistema é projetado para lidar com grande volume de dados, permitindo que cada formulário armazene milhares de respostas.
Além disso, é considerada a possibilidade de um mesmo usuário ter centenas de formulários e um mesmo formulário ter centenas de perguntas.

2. Endpoints e Rotas:

Os dados devem ser enviados em formato Json e também serão retornados neste formato.

Para areas com acesso que necessite token de autenticação, adicionar ao Header: "Authorization" e "token de um usuário cadastrado".

   - User:
     1.POST"/create-user":
     ```
       input: {
              	"nome": "Nome de Teste"
              }

       Output:{
              	"token": "YNBcrOlWUbw1WfR0KBS6HqM55XsGjozjLPRYoHsiwvpSCk7En7qJsrVOI3BJ"
              }
     ```
     2.GET"/show-user":
    ```
       Output:{
                	"id": 5,
                	"nome": "Nome de Teste",
                	"email": "email",
                	"email_verified_at": "datetime",
                	"password": "password",
                	"remember_token": "token",
                	"created_at": "2024-03-22T14:24:44.000000Z",
                	"updated_at": "2024-03-22T20:00:52.000000Z",
                	"limite_respostas": "limite_respostas"
                }
    ```   
   - Forms:
     3.POST"/create-forms"
     ```
       input: {
              	"titulo": "seu titulo",
            		"fonte": "sua fonte",
            		"cor": "cor",
            	 	"url_notificacao": "url_teste"
              }

       Output:{
                "titulo": "seu titulo",
                "fonte": "sua fonte",
                "cor": "cor",
                "id_usuario": "idaqui",
                "url_notificacao": "url_teste",
                "updated_at": "datetime",
                "created_at": "datetime",
                "id": "id_fom"
              }
        ```
  
     4.GET"/show-forms"

     Filtro respostas: 0 - Todas as respostas e 1 - Apenas respostas de quem completou o formulário

     ```
       input: {
                "id": "id do formulario",
                "filtro respostas": "filtro aqui"
              }
     
       Output: {
            	"form": {
                		"id": 1,
                		"titulo": "AlteradoCom@sucess.com",
                		"perguntas": [
                            			{
                            				"id": 1,
                            				"pergunta": "casa",
                            				"respostas": [
                            					{
                            						"id_usuario": "1",
                            						"resposta": "ble"
                            					}
                            				]
                            			}
                                     ]
                         }
                  }
     ```
     
     5.GET"/list-forms"
     ```
         Output:{
                 "form":{
                        "id": 1,
                  			"titulo": "titulo_form",
                  			"fonte": "fonte",
                  			"cor": "cor",
                  			"url_notificacao": "url_notificacao",
                  			"created_at": "datetime",
                  			"updated_at": "datetime",
                  			"id_usuario": "id_usuario"
                        }
                }
     ```
     6.POST"/create-perguntas"

     tipo_resposta: 1 - Simples(considera uma pergunta sem resposta pré cadastrada)
                    2 - Composta(considera uma pergunta e apartir de duas respostas)
                    3 - Estruturada(considera apartir de duas perguntas sem nenhuma resposta pré cadastrada)

     perguntas e respostas: Seguindo a descrição acima e o exemplo abaixo dependendo de qual tipo de resposta escolher você pode adicionar mais respostas adicionando um numero ao lado da "resposta e mais perguntas adicionando ao lado de "pergunta".

          Input:{
                  "id_forms": "1",
                  "tipo_resposta": "3",
                  "pergunta": "casa",
                  "pergunta1": "casa",
                  "resposta": "teste",
                  "resposta1": "2teste"
                 }

           Output:"pergunta": {
                  "id_forms": "1",
                  "tipo_resposta": "3",
                  "pergunta": "casa,casa",
                  "updated_at": "2024-03-22T19:20:05.000000Z",
                  "created_at": "2024-03-22T19:20:05.000000Z",
                  "id": 34
                }
   
     
   - Respostas:
     7.POST"/create-respostas"
     ```
           Input: {
                		"id_forms": "id_formulario",
                		"id_pergunta": "id_pergunta",
                		"resposta": "resposta"
                	}
           Output:{
                  	"id_forms": "id_form",
                  	"id_pergunta": "id_pergunta",
                  	"id_usuario": "id_usuario",
                  	"resposta": "resposta",
                  	"updated_at": "datetime",
                  	"created_at": "datetime",
                  	"id": 44
                  }
     ```
     8.GET"list-respostas"
     ```
         Input:{
                 "id_forms": "id_form"
                }

         Output:{
              		"id": "id_resposta",
              		"id_forms": "id_form",
              		"id_pergunta": "id_pergunta",
              		"id_usuario": "id_usuario",
              		"resposta": "resposta",
              		"created_at": "2024-03-21T14:49:11.000000Z",
              		"updated_at": "2024-03-21T14:49:11.000000Z"
              	}
       ```

Contato

Nome: Geraldo Fulgêncio de Oliveira Bisneto

Email: fulgenciobisneto@gmail.com

LinkedIn: https://www.linkedin.com/in/geraldobisneto/

