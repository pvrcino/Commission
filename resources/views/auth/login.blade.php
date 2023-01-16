<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Commission - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">

    <link href="/css/bootstrap.min.css" rel="stylesheet">
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
            max-width: 250px;
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
        <div class="row px-1" style="max-width: 600px">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <img src="/img/ouro.png" alt="logo" class="logo mb-3">
            </div>
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
            <form action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div class="form-group d-flex justify-content-center align-items-center mb-2">
                    <input type="email" name="email" class="main-input form-control p-3"
                        placeholder="Email" required>
                </div>
                <div class="form-group d-flex justify-content-center align-items-center mb-2">
                    <input type="password" name="senha" class="main-input form-control p-3"
                        placeholder="Senha" required>
                </div>
                <button type="submit" class="main-btn btn btn-lg btn-block btn-light">Entrar</button>
            </form>
            <div>
                <p class="text-white text-center mt-3">Não tem uma conta? <a href="{{ route('auth.register') }}">Cadastre-se</a></p>
            </div>
        </div>
    </div>
</body>

</html>
