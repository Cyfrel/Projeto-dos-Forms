<div>
    <!-- Be present above all else. - Naval Ravikant -->


    <html>

    <head>
        <script src="{{ asset('../js/custom.js') }}"></script>

    </head>
    <a href="{{route('welcome')}}"> Voltar ao Inicio</a><br>

    <h2>Cadastrar Formulario</h2>

    <form action="{{route('forms.store')}}" method="POST">
        @csrf
        
        <label>Titulo: </labe>
        <input type="text" name="titulo" id="titulo" required>

        <label>Fonte: </labe>
        <input type="text" name="fonte" id="fonte" required>

        <label>Cor: </labe>
        <input type="text" name="cor" id="cor" required>

        <div id="formulario">
            <div class="form-group">
                <span id="msgAlerta1"></span>

                <label>Pergunta: </label>
                <input type="text" name="pergunta" id="pergunta" required>

                <select class="form-select" name="tipo" id="tipo" aria-label="Default select example" required>
                    <option selected>Escolha um tipo de Pergunta</option>
                    <option value="1">Dados Simples</option>
                    <option value="2">Dados em Lista</option>
                    <option value="3">Dados Estruturados</option>
                </select>

                <input type="hidden" name="resposta" id="resposta" value='sim'>

                <input type="hidden" name="url_notificacao" id="url_notificacao" value='sim'>

                <button type="button" onclick="adicionarPergunta()"> + </button>

            </div>
        </div>

        <button type="submit"> Enviar </button>

    </form>





    <script src="{{ asset('./js/custom.js') }}"></script>

    
    </html>

    <script src="{{ asset('../js/custom.js') }}"></script>


</div>
