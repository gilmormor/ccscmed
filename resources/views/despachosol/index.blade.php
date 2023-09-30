@extends("theme.$theme.layout")
@section('titulo')
Solicitud de despacho
@endsection
@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/indexnew.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/despachosol/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">Solicitud de Despacho por aprobar</h3>
                <div class="box-tools pull-right">
                    <a href="{{route('listarnv_despachosol')}}" class="btn btn-block btn-success btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Nueva Solicitud Despacho
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table display AllDataTables table-condensed table-hover" id="tabla-data-despachosol" data-page-length='50'>
                        <thead>
                            <tr>
                                <th class="width70 tooltipsC" title='Solicitud de Despacho'>SD</th>
                                <th class='tooltipsC' title='Fecha'>Fecha</th>
                                <th>Razón Social</th>
                                <th class='tooltipsC' title='Orden de Compra'>OC</th>
                                <th class='tooltipsC' title='Nota de Venta'>NV</th>
                                <th class='tooltipsC' title='Precio x Kg'>$ x Kg</th>
                                <th class='tooltipsC' title='Comuna'>Comuna</th>
                                <th class='tooltipsC' title='Total Kg' style="text-align:right">Total Kg</th>
                                <th class='tooltipsC' title='Tipo Entrega'>TE</th>
                                <th class="ocultar">Icono</th>
                                <th class="ocultar">Obs Bloqueo</th>
                                <th class="ocultar">oc_file</th>
                                <th class="ocultar">obsdev</th>
                                <th class="ocultar">updated_at</th>
                                <th class="width70">Acción</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                            </tr>
                            <tr>
                                <th colspan='7' style='text-align:right'>Total página</th>
                                <th id='subtotalkg' name='subtotalkg' style='text-align:right'>0,00</th>
                                <th colspan='2' style='text-align:right'></th>
                            </tr>
                            <tr>
                                <th colspan='7' style='text-align:right'>TOTAL GENERAL</th>
                                <th id='totalkg' name='totalkg' style='text-align:right'>0,00</th>
                                <th colspan='2' style='text-align:right'></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('generales.modalpdf')
@endsection