<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Aqui você pode acessar o token CSRF -->
    <title>Sua Aplicação</title>
</head>
<body>
    <div id="app">
        <!-- Conteúdo da sua aplicação -->
    </div>

    <!-- Adicione o token CSRF como um atributo de data para que você possa acessá-lo com JavaScript -->
    <script>
        window.csrfToken = "{{ csrf_token() }}";
    </script>
</body>
</html>
