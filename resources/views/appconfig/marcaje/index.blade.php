@extends("theme.$theme.layout")
@section('titulo') Config. Marcaje - Perímetros @endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Configuración de Perímetro de Marcaje</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('appconfig.marcaje.exentos') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-user-times"></i> Empleados Exentos
                    </a>
                </div>
            </div>
            <div class="box-body">
                <p class="text-muted">
                    Configure el perímetro GPS de cada empresa. Los empleados que intenten marcar fuera del perímetro (más tolerancia) serán rechazados,
                    salvo que estén registrados como exentos.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Grupo</th>
                                <th class="text-center">Valida perímetro</th>
                                <th class="text-center">Radio (m)</th>
                                <th class="text-center">Tolerancia (m)</th>
                                <th class="text-center">Coordenadas</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($empresas as $emp)
                            <tr>
                                <td>{{ $emp->emp_nombre }}</td>
                                <td>{{ optional($emp->grupo)->gru_nombre ?? $emp->gru_cod }}</td>
                                <td class="text-center">
                                    @if($emp->marc_valida_perimetro)
                                        <span class="label label-success">Activo</span>
                                    @else
                                        <span class="label label-default">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $emp->marc_radio ?? 100 }}</td>
                                <td class="text-center">{{ $emp->marc_tolerancia ?? 50 }}</td>
                                <td class="text-center">
                                    @if($emp->marc_lat && $emp->marc_lng)
                                        <small>{{ number_format($emp->marc_lat,6) }}, {{ number_format($emp->marc_lng,6) }}</small>
                                    @else
                                        <span class="text-muted">Sin configurar</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('appconfig.marcaje.empresa', $emp->emp_codh) }}"
                                       class="btn btn-xs btn-primary">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
