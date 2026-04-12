@extends("theme.$theme.layout")
@section('titulo') Empleados Exentos de Perímetro @endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')

        {{-- Formulario agregar --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-user-plus"></i> Agregar Empleado Exento</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('appconfig.marcaje') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Volver a Empresas
                    </a>
                </div>
            </div>
            <form action="{{ route('appconfig.marcaje.exentos.store') }}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group {{ $errors->has('emp_ced') ? 'has-error' : '' }}">
                                <label>Cédula</label>
                                <input type="text" name="emp_ced" class="form-control"
                                       value="{{ old('emp_ced') }}" placeholder="Ej: 13940498" maxlength="20">
                                @if($errors->has('emp_ced'))
                                    <span class="help-block">{{ $errors->first('emp_ced') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Empresa <small class="text-muted">(vacío = todas)</small></label>
                                <select name="emp_codh" class="selectpicker form-control" data-width="100%">
                                    <option value="">Todas las empresas</option>
                                    @foreach($empresas as $codh => $nombre)
                                        <option value="{{ $codh }}" {{ old('emp_codh') == $codh ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group {{ $errors->has('tipo_exento') ? 'has-error' : '' }}">
                                <label>Tipo</label>
                                <select name="tipo_exento" class="form-control">
                                    @foreach($tipos as $val => $label)
                                        <option value="{{ $val }}" {{ old('tipo_exento') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Descripción / Motivo</label>
                                <input type="text" name="descripcion" class="form-control"
                                       value="{{ old('descripcion') }}" placeholder="Opcional" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Agregar
                    </button>
                </div>
            </form>
        </div>

        {{-- Listado --}}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Empleados registrados como exentos</h3>
            </div>
            <div class="box-body">
                @if($exentos->isEmpty())
                    <p class="text-muted">No hay empleados exentos registrados.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Empresa</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exentos as $ex)
                            @php
                                $nombreEmp = $ex->emp_codh ? ($empresas[$ex->emp_codh] ?? $ex->emp_codh) : 'Todas';
                                $tipoLabel = $tipos[$ex->tipo_exento] ?? $ex->tipo_exento;
                            @endphp
                            <tr>
                                <td>{{ $ex->emp_ced }}</td>
                                <td>
                                    @php
                                        $empleado = \DB::table('nm_empleados')
                                            ->where('emp_ced', $ex->emp_ced)
                                            ->select('emp_nombre')
                                            ->first();
                                    @endphp
                                    {{ $empleado->emp_nombre ?? '—' }}
                                </td>
                                <td>{{ $nombreEmp }}</td>
                                <td>
                                    @if($ex->tipo_exento === 'T')
                                        <span class="label label-info">{{ $tipoLabel }}</span>
                                    @elseif($ex->tipo_exento === 'J')
                                        <span class="label label-warning">{{ $tipoLabel }}</span>
                                    @else
                                        <span class="label label-default">{{ $tipoLabel }}</span>
                                    @endif
                                </td>
                                <td>{{ $ex->descripcion }}</td>
                                <td class="text-center">
                                    <form action="{{ route('appconfig.marcaje.exentos.destroy', $ex->id) }}"
                                          method="POST" style="display:inline"
                                          onsubmit="return confirm('¿Eliminar este registro?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
