@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
    <div class="row mb-4">
        <h1 class="text-center">Produtos</h1>
    </div>
    @foreach ($meusProdutos as $produto)
        @php
            $comissao = $produto->comissao;
            $comissao = number_format($comissao, 2, ',', '.');
        @endphp
        <div class="row mb-2">
            <div class="col mb-2">
                <div class="card bg-black py-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-auto mb-2">
                                <img src="https://app.assistalucre.com.br/img/logo.png" alt="Produto"
                                    style="max-width: 96px">
                            </div>
                            <div class="col">
                                <h5>{{$produto->nome}}</h5>
                                <span>{{$produto->descricao}}</span>
                                <hr class="bg-white">
                                <h5>Sua comissão: R$ {{$comissao}} por venda!</h5>
                                @if ($produto->usersSubsellers)
                                <hr class="bg-white">
                                    <h5>Comissão dividida:</h5>
                                    @foreach ($produto->usersSubsellers as $user)
                                        <span>{{$user->email}} | R$ {{number_format($user->comissao, 2, ",", ".")}}</span>
                                        @if (!$loop->last)
                                            <br>
                                        @endif
                                    @endforeach
                                @endif
                                <hr class="bg-white">
                                <span class="badge text-bg-success">Ativo</span>
                                <span>Link para divulgação: <a
                                        href="https://app.arkama.com.br/af/{{$produto->afiliacao}}">https://app.arkama.com.br/af/{{$produto->afiliacao}}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
        
    @foreach ($produtoSubseller as $produto)
        @php
            $comissao = $produto->comissao;
            $comissao = number_format($comissao, 2, ',', '.');
        @endphp
        <div class="row mb-2">
            <div class="col mb-2">
                <div class="card bg-black py-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-auto mb-2">
                                <img src="https://app.assistalucre.com.br/img/logo.png" alt="Produto"
                                    style="max-width: 96px">
                            </div>
                            <div class="col">
                                <h5>{{$produto->nome}}</h5>
                                <span>{{$produto->descricao}}</span>
                                <hr class="bg-white">
                                <h5>Sua comissão: R$ {{$comissao}} por venda!</h5>
                                <hr class="bg-white">
                                <span class="badge text-bg-success">Ativo</span>
                                <span>Link para divulgação: <a
                                        href="{{$produto->link}}">{{$produto->link}}</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
