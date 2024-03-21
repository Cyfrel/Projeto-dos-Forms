<div>
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    <html>

    <a href="{{route('forms.create')}}">Criar Formulário</a><br>

    <h2>Teste</h2>

    @if(session('sucess'))
        <span style="color: #082;">
            {{session('sucess')}}
        </span>
    @endif

    

    @forelse ($forms as $form)
       Id: {{$form->id}} <br>
       Titulo: {{$form->titulo}} <br>
       Fonte: {{$form->fonte}} <br>
       Cor: {{$form->cor}} <br>
       Pergunta: {{$form->pergunta}} <br>
       Resposta: {{$form->resposta}} <br>
       Url_notific: {{$form->url_notificacao}} <br>
       Tipo: {{$form->tipo}} <br>
       Criada em: {{$form->created_at}} <br>
       Atualizada em: {{$form->updated_at}} <br>
       <br>
    @empty
        <span style="color: #f00;">Nenhum formulário encontrado</span>
    @endforelse
    
    </html>


</div>
