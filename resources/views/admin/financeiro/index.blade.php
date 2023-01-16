@extends('layouts.admin')

@section('title', 'Financeiro')

@section('content')
    <div class="row mb-4">
        <h1 class="text-center">Financeiro</h1>
    </div>

    <hr class="bg-white">
    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-dark table-bordered table-striped text-center">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Usuário</th>
                        <th scope="col">CPF</th>
                        <th scope="col">Data de Solicitação</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Dados Bancários</th>
                        <th scope="col">Data de Pagamento</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($saques as $saque)
                        <tr>
                            <th scope="row">{{ $saque['id'] }}</th>
                            <td> {{ $saque['nome'] }}</td>
                            <td> {{ $saque['email'] }}</td>
                            <td> {{ $saque['cpf'] }}</td>
                            <td>{{ $saque['created_at']->format('d/m/Y H:i:s') }}</td>
                            <td>R$ {{ number_format($saque['valor'], 2, ",", ".") }}</td>
                            <td>Banco: {{$saque['bank']->bank_name . " (" . $saque['bank']->bank_code . ")"}}<br>
                                Agencia: {{$saque['bank']->agency}}<br>
                            Conta: {{$saque['bank']->account . "-" . $saque["bank"]->account_digit}}</td>
                            @if ($saque['paid_at'])
                                <td>{{ $saque['paid_at']->format('d/m/Y H:i:s') }}</td>
                            @else
                                <td></td>
                            @endif
                            @php
                                $status = $saque['status'];
                                if ($status == 0) {
                                    echo "<td><span class='badge text-bg-warning'>Pendente</span></td>";
                                } elseif ($status == 1) {
                                    echo "<td><span class='badge text-bg-success'>Pago</span></td>";
                                } else {
                                    echo "<td><span class='badge text-bg-danger'>Cancelado</span></td>";
                                }
                            @endphp

                            @if ($saque['status'] == 0)
                                <td class="d-flex justify-content-around align-items-center">
                                    <form action="/admin/financeiro/{{$saque['id']}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="method" value="pago"/>
                                        <button type="submit" class="btn btn-success">Pagar</button>
                                    </form>
                                    <form action="/admin/financeiro/{{$saque['id']}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="method" value="recusar"/>
                                        <button type="submit" class="btn btn-danger">Cancelar</button>
                                    </form>
                                </td>
                            @else
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p>Pagina {{ $saques->currentPage() }} de {{ $saques->lastPage() }} | Total de saques: {{ $saques->total() }}</p>
        <div class="d-flex justify-content-center" id="pagination">
            @if (isset($search))
                {{ $saques->appends(['search' => $search])->links() }}
            @else
                {{ $saques->links() }}
            @endif
        </div>

    </div>
@endsection
