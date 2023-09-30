@extends("theme.$theme.layout")
@section('titulo')
Devolver Nota de Venta a Vendedor
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/notaventadevolvend/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Devolver Nota de Venta a Vendedor</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <!--<table class="table table-striped table-bordered table-hover" id="tabla-datadesc">-->
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-datadesc">
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th class="width30">Nro Cot</th>
                                <th>Fecha</th>
                                <th>Razon Social</th>
                                <th>Vendedor</th>
                                <th class="width50"><label for="" title='PDF' data-toggle='tooltip'>PDF</label></th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</div>

@include('generales.modalpdf')
@endsection