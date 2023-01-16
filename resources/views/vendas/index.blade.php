@extends('layouts.app')

@section('title', 'Vendas')

@section('head')
    <style>
        .svg-pix {
            fill: #32bbad;
            width: 17px;
        }
    </style>
@endsection
@section('content')
    <div class="row mb-4">
        <h1 class="text-center">Vendas</h1>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-dark table-bordered table-striped text-center">
                <div class="d-flex justify-content-center">
                    @if (isset($search))
                        {{ $vendas->appends(['search' => $search])->links() }}
                    @else
                        {{ $vendas->links() }}
                    @endif
                </div>
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Data da Compra</th>
                        <th scope="col">Comprador</th>
                        <th scope="col">Produto</th>
                        <th scope="col">Comissão</th>
                        <th scope="col">Forma de Pagamento</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($vendas as $venda)
                        <tr>
                            <th scope="row">{{ $venda['id'] }}</th>
                            <td>{{ $venda['created_at']->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $venda['comprador'] }}</td>
                            <td>{{ $venda->produto->nome }}</td>
                            <td>R$ {{ number_format($venda->comissao,2, ",", ".") }}</td>
                            @php
                                $tipo = $venda->payment_type;
                                if ($tipo == 1) {
                                    echo '<td><img src="img/icon-credit-card.png" class="svg-pix"> Cartão de Crédito</td>';
                                } elseif ($tipo == 2) {
                                    echo '<td><img src="img/icon-pix.svg" class="svg-pix"> Pix</td>';
                                }
                                $status = $venda->status;
                                if ($status == 1) {
                                    echo "<td><span class='badge text-bg-success'>Aprovado</span></td>";
                                } elseif ($status == 0) {
                                    echo "<td><span class='badge text-bg-warning'>Pagamento Pendente</span></td>";
                                } elseif ($status == 2) {
                                    echo "<td><span class='badge text-bg-danger'>Reprovado</span></td>";
                                } elseif ($status == 3) {
                                    echo "<td><span class='badge text-bg-danger'>Reembolsado</span></td>";
                                }
                            @endphp
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p>Pagina {{ $vendas->currentPage() }} de {{ $vendas->lastPage() }} | Total de vendas: {{ $vendas->total() }}</p>
        <div class="d-flex justify-content-center" id="pagination">
            @if (isset($search))
                {{ $vendas->appends(['search' => $search])->links() }}
            @else
                {{ $vendas->links() }}
            @endif
        </div>

    </div>
@endsection
