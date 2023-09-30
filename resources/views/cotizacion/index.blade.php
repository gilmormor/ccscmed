@extends("theme.$theme.layout")
@section('titulo')
Cotización
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/cotizacion/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Cotización</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_cotizacion')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Crear Cotización
                    </a>
                </div>                        
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-data-cotizacion">-->
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-cotizacion">
                        <thead>
                            <tr>
                                <th class="width70">ID</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th class='tooltipsC' title='Ver PDF Cotizacion'>PDF</th>
                                <th class="ocultar">aprobstatus</th>
                                <th class="ocultar">aprobobs</th>
                                <th class="ocultar">contador</th>
                                <th class="ocultar">updated_at</th>
                                <th class="ocultar">contacutec</th>
                                <th class="width150">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="todo-list1">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection