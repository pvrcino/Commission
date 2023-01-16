@extends('layouts.app')

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
            
            $saldoDisponivel = $user->saldoDisponivel;
            $saldoDisponivel = number_format($saldoDisponivel, 2, ',', '.');
            
        @endphp
    </div>
    <div class="row mb-2">
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bx-trending-up icon'></i>
                    <div class="row">
                        <h5 class="text-end">Vendas no dia</h5>
                        <span class="text-end">R$ {{ $vendasDia }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bxs-calendar icon'></i>
                    <div class="row">
                        <h5 class="text-end">Vendas no mês</h5>
                        <span class="text-end">R$ {{ $vendasMes }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm mb-2">
            <div class="card bg-black py-3">
                <div class="card-body d-flex justify-content-between">
                    <i class='bx bxs-wallet icon'></i>
                    <div class="row">
                        <h5 class="text-end">Saldo Disponível</h5>
                        <span class="text-end">R$ {{ $saldoDisponivel }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="chart" class="text-dark"></div>
    </div>

@endsection

@section('scripts')
    @php
        $datas = [];
        $faturamentos = [];
        foreach ($vendas4Dias as $venda) {
            $data = date('d/m/Y', strtotime($venda->data));
            $faturamento = $venda->ganhos;
            $datas[] = $data;
            $faturamentos[] = $faturamento;
        }
        $datas = json_encode($datas);
        $faturamentos = json_encode($faturamentos);
    @endphp
    <script>
        var options = {
            chart: {
                type: 'bar'
            },
            series: [{
                name: 'Faturamento',
                data: @php echo $faturamentos; @endphp
            }],
            xaxis: {
                categories: @php echo $datas; @endphp
            },
            fill: {
                colors: ['#0aa10a']
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    dataLabels: {
                        position: 'bottom'
                    }
                }
            },
        }

        var chart = new ApexCharts(document.querySelector("#chart"), options);

        chart.render();
    </script>

@endsection
