@extends("theme.$theme.layout")
@section('titulo')
Unidad de Medida
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/categoriagrupovalmes/index.js")}}" type="text/javascript"></script>
@endsection
<?php
    use App\Models\categoriagrupovalmes;
    $aux_mesanno = categoriagrupovalmes::mesanno(date("Y") . date("m"));
?>
@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Costo por Categoria y grupo</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('crear_categoriagrupovalmes')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nuevo registro
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-offset-3 col-xs-12 col-md-5 col-sm-5">
                        <div class="form-group">
                            <div class="col-xs-12 col-md-2 col-sm-2 text-left">
                                <label for="annomes" class="control-label">Fecha:</label>
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6">
                                <input type="text" name="annomes" id="annomes" class="form-control date-picker" value="{{old('annomes', $aux_mesanno ?? '')}}" readonly required>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover" id="tabla-data" data-page-length="25">
                    <thead>
                        <tr>
                            <th class="width70">ID</th>
                            <th>Año Mes</th>
                            <th>Categoria</th>
                            <th>Grupo</th>
                            <th>Costo</th>
                            <th class="width70"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection