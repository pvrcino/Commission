@extends('layouts.admin')

@section('title', 'Afiliações')

@section('content')
    <div class="row mt-4">
        <h1 class="text-center mb-3">Afiliações</h1>
        <button class="btn btn-success ml-auto" data-bs-toggle="modal" data-bs-target="#modalAdd">Adicionar</button>
    </div>

    @include('admin.afiliacoes.pagination', ['afiliacoes' => $afiliacoes])

@endsection

@section('modals')
    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="modalAddLabel">Cadastrar afiliado</h5>
                </div>
                <form id="formCadastrar" action="{{ route('admin.afiliacao.cadastrar') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputNome">E-mail</label>
                            <input type="email" class="form-control" placeholder="arkama@arkama.com.br" name="email" required />
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">R$</label>
                            <input type="numeric" class="form-control" placeholder="Comissão" name="comissao" required />
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">Produto</label>
                            <select class="form-select" id="produto" name="produto_id" required>
                                @if (count($produtos) == 0)
                                    <option value="" disabled selected>Nenhum produto cadastrado.</option>
                                @else
                                    @foreach ($produtos as $produto)
                                        <option value="{{ $produto->id }}">{{ $produto->nome }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">ID</label>
                            <input type="numeric" class="form-control" placeholder="ID do Plano" name="plan_id" required />
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="inputGroupSelect01">Link</label>
                            <input type="numeric" class="form-control" placeholder="Link" name="link" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="cadastrar">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
