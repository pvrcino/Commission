
<div class="row mt-2">
    <div class="table-responsive">
        <table class="table table-dark table-bordered table-striped text-center">
            <div class="d-flex justify-content-center">
                @if (isset($search))
                    {{ $afiliacoes->appends(['search' => $search])->links() }}
                @else
                    {{ $afiliacoes->links() }}
                @endif
            </div>
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">ID do Plano</th>
                    <th scope="col">Vendedor</th>
                    <th scope="col">Comissão</th>
                    <th scope="col">Link</th>
                    <th scope="col">Revendedores</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($afiliacoes as $afiliacao)
                    <tr>
                        <th scope="row">{{ $afiliacao['id'] }}</th>
                        <td>{{ $afiliacao['plan_id'] }}</td>
                        <td>{{ $afiliacao['user']->email }}</td>
                        <td>R$ {{ number_format($afiliacao['comissao'], 2, ',', '.') }}</td>
                        <td>{{ $afiliacao['link'] }}</td>
                        @if ($afiliacao['subseller'] == null)
                            <td><span class="badge text-bg-danger">Nenhum</span></td>
                        @else
                            <td>
                                @foreach($afiliacao["subseller"] as $subseller)
                                    <span class="badge text-bg-success">{{ \App\Models\User::find($subseller["id"])->email }} - R$ {{number_format($subseller['comissao'], 2, ",", ".")}}</span>
                                    @if(!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p>Pagina {{ $afiliacoes->currentPage() }} de {{ $afiliacoes->lastPage() }} | Total de afiliações: {{ $afiliacoes->total() }}
    </p>
    <div class="d-flex justify-content-center" id="pagination">
        @if (isset($search))
            {{ $afiliacoes->appends(['search' => $search])->links() }}
        @else
            {{ $afiliacoes->links() }}
        @endif
    </div>
</div>
