@extends("theme.$theme.layout")
@section('titulo')
Notas de Venta Cerradas
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/notaventacerrar/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Notas de Venta Cerradas</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_notaventacerrada')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo Cierre
                    </a>
                </div>
            </div>
            <div class="box-body">
                <!--<table class="table table-striped table-bordered table-hover" id="tabla-data">-->
                <table class="table display AllDataTables table-hover table-condensed table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Observación</th>
                            <th class="width100">NotaVenta</th>
                            <th class="width30">Acción</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection