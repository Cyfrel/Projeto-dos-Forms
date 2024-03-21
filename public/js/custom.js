


var controlePergunta = 1;

function adicionarPergunta(){
    controlePergunta++;
    document.getElementById('formulario').insertAdjacentHTML('beforeend', '<div class="form-group" id="conjunto'+ controlePergunta +'"><span id="msgAlerta'+ controlePergunta +'"></span><label>Pergunta: </label><input type="text" name="pergunta'+ controlePergunta +'" id="pergunta'+ controlePergunta +'" required> <select class="form-select" name="tipo'+ controlePergunta +'" id="tipo'+ controlePergunta +'" aria-label="Default select example" required><option selected>Escolha um tipo de Pergunta</option> <option value="1">Dados Simples</option><option value="2">Dados em Lista</option><option value="3">Dados Estruturados</option></select><button type="button" id="" onclick="removerPergunta('+ controlePergunta +')"> - </button></div>');

}

function removerPergunta(idPergunta){
    document.getElementById('conjunto' + idPergunta).remove();
}

function adicionarResposta(tipoPergunta){
    document.getElementById('tipo'+ controlePergunta +'').insertAdjacentHTML('beforeend', '<div class="form-group" id="conjunto'+ controlePergunta +'"><span id="msgAlerta'+ controlePergunta +'"></span><label>Pergunta: </label><input type="text" name="pergunta'+ controlePergunta +'" id="pergunta'+ controlePergunta +'">');
}