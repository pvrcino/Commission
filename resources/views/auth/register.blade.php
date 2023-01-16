<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Commission - Cadastro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="57x57" href="/icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icon/favicon-16x16.png">
    <link rel="manifest" href="/icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/icon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <style>
        * {
            font-family: 'Lato', sans-serif;
        }

        body {
            height: 100vh;
        }

        .centr {
            height: 100%;
        }

        .logo {
            width: 100%;
            max-width: 480px;
        }

        .main-btn {
            width: 100%;
        }


        .main-input {
            width: 100%;
            font-weight: 600px;
        }
    </style>
</head>

<body class="bg-dark">
    <div class="centr container d-flex align-items-center justify-content-center">
        <div class="row">
            <div class="col-6">
                <div class="d-flex align-items-center justify-content-center">
                    <img src="/img/ouro.png" alt="logo" class="logo mb-3">
                </div>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-center">
                <div class="row" style="width: 100%">
                    @if ($errors->any())
                        <div class="alert alert-danger my-2" role="alert">
                            @foreach ($errors->all() as $error)
                                <span>{{ $error }}</span>
                                @if (!$loop->last)
                                    <br>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('auth.register.request') }}" method="POST">
                        @csrf
                        <div class="form-group d-flex justify-content-center align-items-center mb-2">
                            <input type="text" name="nome" class="main-input form-control p-3"
                                placeholder="Nome Completo" required>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center mb-2">
                            <input type="text" name="document" class="main-input form-control p-3"
                                placeholder="CPF / CNPJ (Sem pontuação)" required>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center mb-2">
                            <input type="text" name="telefone" class="main-input form-control p-3"
                                placeholder="Telefone / Celular" required>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center mb-2">
                            <input type="text" name="email" class="main-input form-control p-3" placeholder="Email"
                                required>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center mb-2">
                            <input type="password" name="senha" class="main-input form-control p-3"
                                placeholder="Senha" required>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center mb-2">
                            <input type="password" name="senhaConfirmada" class="main-input form-control p-3"
                                placeholder="Confirme sua senha" required>
                        </div>
                        <button type="submit" class="main-btn btn btn-lg btn-block btn-light">Cadastrar</button>

                    </form>
                    <div>
                        <p class="text-white text-center mt-3">Já possui uma conta? <a
                                href="{{ route('auth.login') }}">Acesse já</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
