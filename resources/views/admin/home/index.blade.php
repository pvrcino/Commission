@extends('layouts.admin')

@section('title', 'Dashboard')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .icon {
            font-size: 3.5rem;
            color: #0aa10a;
        }
    </style>


@endsection


@section('content')
    <div class="row mb-4">
        @php
            $user = Auth::user();

            $lucroDia = number_format($lucroDia, 2, ',', '.');
            $lucroMes = number_format($lucroMes, 2, ',', '.');
            $saldoAPagar = number_format($saldoAPagar, 2, ',', '.');
            $saldosDisponiveis = number_format($saldosDisponiveis, 2, ",", ".");
            $saldosPendentes = number_format($saldosPendentes, 2, ",", ".");
            
        @endphp
    </div>
    <div class="row mb-2">
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bx-trending-up icon'></i>
                    <div class="row">
                        <h5 class="text-end">Lucro no Dia</h5>
                        <span class="text-end">R$ {{ $lucroDia }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bxs-calendar icon'></i>
                    <div class="row">
                        <h5 class="text-end">Lucro no Mês</h5>
                        <span class="text-end">R$ {{ $lucroMes }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bxs-wallet icon'></i>
                    <div class="row">
                        <h5 class="text-end">Saques Pendentes</h5>
                        <span class="text-end">R$ {{ $saldoAPagar }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bx-trending-up icon'></i>
                    <div class="row">
                        <h5 class="text-end">Saldos Disponíveis</h5>
                        <span class="text-end">R$ {{ $saldosDisponiveis }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bxs-calendar icon'></i>
                    <div class="row">
                        <h5 class="text-end">Saldo Pendentes</h5>
                        <span class="text-end">R$ {{ $saldosPendentes }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include("admin.vendas.pagination", ["vendas" => $vendas])

@endsection


