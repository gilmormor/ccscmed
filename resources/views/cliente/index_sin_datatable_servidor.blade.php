@extends("theme.$theme.layout")
@section('titulo')
Clientes
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cliente/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Clientes</h3>
                @if(can('guardar-cliente',false) == true)
                    <div class="box-tools pull-right">
                        <a href="{{route('crear_cliente')}}" class="btn btn-block btn-success btn-sm">
                            <i class="fa fa-fw fa-plus-circle"></i> Crear Cliente
                        </a>
                    </div>
                @endif
            </div>
            <div class="box-body">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th class="width70">RUT</th>
                            <th>Nombre</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_nfila = 0; ?>
                        @foreach ($datas as $data)
                        <?php $aux_nfila++; ?>
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->rut}}</td>
                            <td>{{$data->razonsocial}}</td>
                            <td>
                                @if(can('guardar-cliente',false) == true)
                                    <a href="{{route('editar_cliente', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                        <i class="fa fa-fw fa-pencil"></i>
                                    </a>
                                @else
                                    <a href="{{route('editar_cliente', ['id' => $data->id])}}" class="btn-accion-tabla tooltipsC" title="Ver este registro">
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>
                                @endif
                                @if(can('eliminar-cliente',false) == true)
                                    <form action="{{route('eliminar_cliente', ['id' => $data->id])}}" class="d-inline form-eliminar" method="POST">
                                        @csrf @method("delete")
                                        <button type="submit" class="btn-accion-tabla eliminar tooltipsC" title="Eliminar este registro">
                                            <i class="fa fa-fw fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection