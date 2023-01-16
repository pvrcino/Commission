@extends('layouts.app')

@section('title', 'Financeiro')

@section('content')
    @php
        $user = Auth::user();
        $saldoDisponivel = $user->saldoDisponivel;
        $saldoDisponivel = number_format($saldoDisponivel, 2, ',', '.');
        
        $saldoPendente = $user->saldoPendente;
        $saldoPendente = number_format($saldoPendente, 2, ',', '.');
    @endphp
    <div class="row mb-4">
        <h1 class="text-center">Financeiro</h1>
    </div>
    <div class="row">
        <div class="col mb-2">
            <div class="card bg-success py-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div
                            class="d-flex align-items-center justify-content-center text-center rounded-circle rounded-icon ">
                            <i class="bx bx-trending-up mx-auto fs-xl"></i>
                        </div>
                        <p class="mb-0 small fw-medium text-uppercase">Saldo disponível</p>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-8">
                            <span class="">Disponível para saque</h5><br>
                                <span class="">R$ {{ $saldoDisponivel }}</h5>
                        </div>
                        <div class="col-sm-4 d-flex justify-content-center align-items-center">
                            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalSaque"
                                style="width: 100%">Solicitar Saque</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="col mb-2">
                <div class="card bg-secondary py-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div
                                class="d-flex align-items-center justify-content-center text-center rounded-circle rounded-icon ">
                                <i class="bx bx-trending-up mx-auto fs-xl"></i>
                            </div>
                            <p class="mb-0 small fw-medium text-uppercase">Saldo pendente</p>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-8">
                                <span class="">Liberado nos próximos dias</h5><br>
                                    <span class="">R$ {{ $saldoPendente }}</h5>
                            </div>
                            <div class="col-sm-4">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="bg-white">
    <div class="row justify-content-center align-items-center mb-2">
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
        @php
            $banks = $user->bankAccounts()->get();
        @endphp
        <div class="row">
            @if (count($banks) > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover text-center">
                        <thead clas="text-white">
                            <tr>
                                <th scope="col">Banco</th>
                                <th scope="col">Agência</th>
                                <th scope="col">Conta</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banks as $bank)
                                <tr>
                                    <td>{{ $bank->bank_name }}</td>
                                    <td>{{ $bank->agency }}</td>
                                    <td>{{ $bank->account . '-' . "$bank->account_digit" }}</td>
                                    <td>
                                        <a class="btn btn-danger btn-sm excluir"
                                            onclick="excluir({{ $bank->id }})">Excluir</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center">Não há contas bancárias cadastradas</p>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddBank">
                    Adicionar Conta
                </button>
            @endif
        </div>
        {{-- <form action="{{ route('financeiro.solicitar') }}" method="POST" style="max-width: 450px;">
            @csrf
            @if (Auth::user()->pix)
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Chave PIX" name="pix"
                        value="{{ Auth::user()->pix }}" required>
                </div>
            @else
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Chave PIX" name="pix" required>
                </div>
            @endif
            <div class="input-group mb-3">
                <input type="number" class="form-control" placeholder="Valor" name="valor" required>
            </div>
            <p class="text-center">Taxa de saque: R$ 3,90</p>
            <button class="btn btn-success type="submit" style="width: 100%">Solicitar Saque</button>
        </form> --}}



    </div>

    <hr class="bg-white">
    <div class="row">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover text-center">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Data de Solicitação</th>
                        <th scope="col">Valor</th>
                        <th scope="col">Data de Pagamento</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($saques as $saque)
                        <tr>
                            <th scope="row">{{ $saque['id'] }}</th>
                            <td>{{ $saque['created_at']->format('d/m/Y H:i:s') }}</td>
                            <td>R$ {{ number_format($saque['valor'], 2, ',', '.') }}</td>
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
@section('modals')
    <div class="modal fade" id="modalAddBank" tabindex="-1" aria-labelledby="modalAddBankLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="modalAddBankLabel">Cadastrar Conta</h5>
                </div>
                <form id="formAddBank" action="{{ route('financeiro.cadastrarConta') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="input-group mb-3">
                            <select class="form-select" id="bankcode" name="bankcode" required>
                                @foreach ($banksApi as $bank)
                                    <option value="{{ $bank->code }}">{{ $bank->code }} - {{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Agência" name="agency" required
                                pattern="[0-9]{1,4}" />
                        </div>
                        <div class="input-group mb-3">
                            <div class="col-9">
                                <input type="text" class="form-control" placeholder="Conta" name="account" required />
                            </div>
                            <div class="col-3">
                                <input type="number" class="form-control" placeholder="Digito" name="account_digit"
                                    min="0" max="9" required />
                            </div>
                        </div>
                        <span class="text-white">A conta bancária precisa estar no nome de:<br>
                            {{ strtoupper($user->nome) }}.</span><br>
                        <span class="text-white">Documento: {{ $user->document }}</span><br>
                        <span class="text-white">Caso os dados estejam incorretos, contate a administração.</span><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="cadastrar">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalSaque" tabindex="-1" aria-labelledby="modalSaqueLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="modalSaqueLabel">Solicitar Saque</h5>
                </div>
                <form id="formSacar" action="{{ route('financeiro.solicitar') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">Banco</label>
                            <select class="form-select" id="bank_account" name="bank_account" required>
                                @if (count($banks) == 0)
                                    <option value="" disabled selected>Nenhum banco cadastrado.</option>
                                @else
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }} -
                                            {{ $bank->account . '-' . $bank->account_digit }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">R$</label>
                            <input type="numeric" class="form-control" placeholder="Valor" name="valor" required />
                        </div>
                        <span class="text-white text-end">Taxa de saque: R$ 6,90</span><br>
                        <span class="text-white text-end">Os saques são realizados em até 2 dias úteis.</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="cadastrar">Solicitar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function excluir(id) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Você não poderá reverter isso!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/contas/' + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            Swal.fire(
                                'Excluído!',
                                'Sua conta foi excluída.',
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            })
                        }
                    });
                }
            })
        }
    </script>
@endsection
