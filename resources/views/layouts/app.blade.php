<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ARKAMA - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <script src="/js/jquery-3.6.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
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
            font-family: 'Roboto', sans-serif;
        }

        .minlogo {
            max-width: 60px;
        }

        .main-btn {
            width: 100%;
        }

        .main-input {
            width: 100%;
            font-weight: 600px;
        }

        #logout {
            color: white;
        }

        :root {
            --header-height: 3rem;
            --nav-width: 68px;
            --first-color: #000000;
            --first-color-light: #AFA5D9;
            --white-color: #F7F6FB;
            --normal-font-size: 1rem;
            --z-fixed: 30
        }

        *,
        ::before,
        ::after {
            box-sizing: border-box
        }

        body {

            margin: var(--header-height) 0 0 0;
            padding: 0 1rem;
            font-family: var(--body-font);
            font-size: var(--normal-font-size);
            transition: .5s
        }

        a {
            text-decoration: none
        }

        .header {
            width: 100%;
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: var(--first-color);
            z-index: var(--z-fixed);
            transition: .5s
        }

        .header_toggle {
            color: var(--first-color);
            font-size: 1.5rem;
            cursor: pointer
        }

        .header_img {
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            border-radius: 50%;
            overflow: hidden
        }

        .header_img img {
            width: 40px
        }

        .l-navbar {
            position: fixed;
            top: 0;
            left: -30%;
            width: var(--nav-width);
            height: 100vh;
            background-color: var(--first-color);
            padding: .5rem 1rem 0 0;
            transition: .5s;
            z-index: var(--z-fixed)
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden
        }

        .nav_logo,
        .nav_link {
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: .5rem 0 .5rem 1.5rem
        }

        .nav_logo {
            margin-bottom: 2rem
        }

        .nav_logo-icon {
            font-size: 1.25rem;
            color: var(--white-color)
        }

        .nav_logo-name {
            color: var(--white-color);
            font-weight: 700
        }

        .nav_link {
            position: relative;
            color: var(--first-color-light);
            margin-bottom: 1.5rem;
            transition: .3s
        }

        .nav_link:hover {
            color: var(--white-color)
        }

        .nav_icon {
            font-size: 1.25rem
        }

        .showd {
            left: 0
        }

        .body-pd {
            padding-left: calc(var(--nav-width) + 1rem)
        }

        .active {
            color: var(--white-color)
        }

        .active::before {
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: var(--white-color)
        }

        .height-100 {
            height: 100vh
        }

        .logo-sm {
            max-width: 20px;
        }

        @media screen and (min-width: 768px) {
            body {
                margin: calc(var(--header-height) + 1rem) 0 0 0;
                padding-left: calc(var(--nav-width) + 2rem)
            }

            .header {
                height: calc(var(--header-height) + 1rem);
                padding: 0 2rem 0 calc(var(--nav-width) + 2rem)
            }

            .header_img {
                width: 40px;
                height: 40px
            }

            .header_img img {
                width: 45px
            }

            .l-navbar {
                left: 0;
                padding: 1rem 1rem 0 0
            }

            .showd {
                width: calc(var(--nav-width) + 156px)
            }

            .body-pd {
                padding-left: calc(var(--nav-width) + 188px)
            }
        }

        .lightcolor {
            color: var(--first-color-light);
        }
    </style>
    @yield('head')
</head>
@php
    $user = Auth::user();
    $splitName = explode(' ', $user->nome, 2);
    $first_name = $splitName[0];
@endphp


<body id="body-pd" class="bg-dark">
    @yield('modals')
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu lightcolor' id="header-toggle"></i> </div>
        <div class="d-flex align-items-center">
            <span class="lightcolor px-2">Olá {{ $first_name }}</span>
            <i class='bx bx-user lightcolor'></i>
        </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="#" class="nav_logo">
                    <img src="/img/ouro.png" alt="" class="logo-sm">
                    <span class="nav_logo-name">Arkama</span>
                </a>
                <div class="nav_list">
                    <a href="{{ route('home.index') }}" class="nav_link">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="{{ route('produtos.index') }}" class="nav_link">
                        <i class='bx bx-package nav_icon'></i>
                        <span class="nav_name">Produtos</span>
                    </a>
                    <a href="{{ route('vendas.index') }}" class="nav_link">
                        <i class='bx bx-list-ul nav_icon'></i>
                        <span class="nav_name">Vendas</span>
                    </a>
                    <a href="{{ route('financeiro.index') }}" class="nav_link">
                        <i class='bx bx-wallet nav_icon'></i>
                        <span class="nav_name">Financeiro</span>
                    </a>
                </div>
            </div>
            <div class="nav_list">
                @if ($user->privilegio >= 1)
                    <a href="{{ route('admin.home.index') }}" class="nav_link">
                        <i class='bx bx-cog nav_icon'></i>
                        <span class="nav_name">Admin</span>
                    </a>
                @endif
                <a href="/logout" class="nav_link">
                    <i class='bx bx-log-out nav_icon'></i>
                    <span class="nav_name">Deslogar</span>
                </a>
            </div>
        </nav>
    </div>
    <div class="container text-white py-4">
        @yield('content')
    </div>
    @yield('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {

            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId)

                // Validate that all variables exist
                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        // show navbar
                        nav.classList.toggle('showd')
                        // change icon
                        toggle.classList.toggle('bx-x')
                        // add padding to body
                        bodypd.classList.toggle('body-pd')
                        // add padding to header
                        headerpd.classList.toggle('body-pd')
                    })
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')
            const linkColor = document.querySelectorAll('.nav_link')
            @php
                $route = Route::currentRouteName();
                $route = explode('.', $route);
                $route = $route[0];
            @endphp
            if ("{{ $route }}" == "home") {
                linkColor[0].classList.add('active')
            } else if ("{{ $route }}" == ("produtos")) {
                linkColor[1].classList.add('active')
            } else if ("{{ $route }}" == ("vendas")) {
                linkColor[2].classList.add('active')
            } else if ("{{ $route }}" == ("financeiro")) {
                linkColor[3].classList.add('active')
            }
        });
    </script>
</body>

</html>
