@extends("theme.$theme.layout")
@section('titulo')
Productos
@endsection

@section("scripts")
    <script type="text/javascript" src="{{asset("assets/js/jquery-barcode.js")}}"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/producto/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        @csrf @method("delete")
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Productos</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_producto')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <!--<table id="tabla-data-productos" class="table-striped table-hover display" style="width:100%">-->
                        <thead>
                            <tr>
                                <th class="width30">ID</th>
                                <th>Nombre</th>
                                <th>Categoria</th>
                                <th>Grupo</th>
                                <th>Diametro</th>
                                <th>Espesor mm</th>
                                <th>Largo</th>
                                <th>Peso</th>
                                <th>Tipo Union</th>
                                <th>Precio</th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection