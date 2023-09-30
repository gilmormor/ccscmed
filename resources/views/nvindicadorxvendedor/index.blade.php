@extends("theme.$theme.layout")
@section('titulo')
Productos Notas de Venta
@endsection

@section("scripts")
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
    <script src="{{autoVer("assets/pages/scripts/nvindicadorxvendedor/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Indicadores Factura por Vendedor</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <form action="{{route('prodxnotaventa_exportPdf')}}" class="d-inline form-eliminar" method="get" target="_blank">
                        @csrf
                        <div class="col-xs-12 col-md-7 col-sm-7">
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Inicial">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="fecha">Fecha Ini:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" bsDaterangepicker class="form-control datepicker" name="fechad" id="fechad" placeholder="DD/MM/AAAA" value="{{old('fechad', $fechaServ['fecha1erDiaMes'] ?? '')}}" required readonly="">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Fecha Fin">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="dep_fecha">Fecha Fin:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <input type="text" class="form-control datepicker" name="fechah" id="fechah" value="{{old('fechah', $fechaServ['fechaAct'] ?? '')}}" placeholder="DD/MM/AAAA" required readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Vendedor">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Vendedor:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="vendedor_id" id="vendedor_id" multiple class="selectpicker form-control vendedor_id" title='Todos...'>
                                            @foreach($vendedores1 as $vendedor)
                                                <option
                                                    value="{{$vendedor->id}}"
                                                    >
                                                    {{$vendedor->nombre}} {{$vendedor->apellido}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Giro">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label for="giro_id">Giro:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="giro_id" id="giro_id" class="form-control selectpicker giro_id">
                                            <option value="">Todos</option>
                                            @foreach($giros as $giro)
                                                <option
                                                    value="{{$giro->id}}"
                                                    >
                                                    {{$giro->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-12 col-sm-12">
                                <div class="col-xs-12 col-md-6 col-sm-6" data-toggle='tooltip' title="Categoría">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label>Categoría:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="categoriaprod_id" id="categoriaprod_id" class="form-control selectpicker categoriaprod_id">
                                            <option value="">Todos</option>
                                            @foreach($categoriaprods as $categoriaprod)
                                                <option
                                                    value="{{$categoriaprod->id}}">
                                                    {{$categoriaprod->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6" data-toggle='tooltip' title="Area de Producción">
                                    <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                        <label >Area Prod:</label>
                                    </div>
                                    <div class="col-xs-12 col-md-8 col-sm-8">
                                        <select name="areaproduccion_id" id="areaproduccion_id" class="selectpicker form-control areaproduccion_id">
                                            @foreach($areaproduccions as $areaproduccion)
                                                <option
                                                    value="{{$areaproduccion->id}}"
                                                    @if ($areaproduccion->id==1)
                                                        {{'selected'}}
                                                    @endif
                                                    >
                                                    {{$areaproduccion->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-5 col-sm-5">
                            <div class="col-xs-12 col-md-6 col-sm-6">
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Consulta:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="consulta_id" id="consulta_id" class="selectpicker form-control consulta_id">
                                        <option value="1">Nota de Venta</option>
                                        <option value="2">Facturado (Fecha FC)</option>
                                        <option value="3">Facturado (Fecha NV)</option>
                                    </select>
                                </div>
                                <div class="col-xs-12 col-md-4 col-sm-4 text-left">
                                    <label>Estatus NV:</label>
                                </div>
                                <div class="col-xs-12 col-md-8 col-sm-8">
                                    <select name="statusact_id" id="statusact_id" class="selectpicker form-control statusact_id">
                                        <option value="1" selected>Activas</option>
                                        <option value="2">Cerradas</option>
                                        <option value="3">Todas: Activas + cerradas</option>
                                    </select>
                                </div>    
                            </div>
                            <div class="col-xs-12 col-md-6 col-sm-6">
                                <button type="button" id="btnconsultar" name="btnconsultar" class="btn btn-success" data-toggle='tooltip' title="Consultar">Consultar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>



            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <legend></legend>
                    </div>
                </div>
			</div>
        </div>
</div>


<div class="row" id="reporte1" name="reporte1" style="display:none;">
    <!-- Custom Tabs -->
    <div class="nav-tabs-custom" id="tabs">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab"  id="tab1" name="tab1">Cantidad</a></li>
            <li><a href="#tab_2" data-toggle="tab" id="tab2" name="tab2">Facturado</a></li>
            <li><a href="#tab_3" data-toggle="tab" id="tab3" name="tab3">Productos</a></li>
            <li><a href="#tab_4" data-toggle="tab" id="tab4" name="tab4">Nota Venta vs Facturado</a></li>
            <li><a href="#tab_5" data-toggle="tab" id="tab5" name="tab5">Meta Comercial</a></li>
            <li><a href="#tab_6" data-toggle="tab" id="tab6" name="tab6">Por area de Producción</a></li>
            <li><a href="#tab_7" data-toggle="tab" id="tab7" name="tab7">Productos $</a></li>
            <li><a href="#tab_8" data-toggle="tab" id="tab8" name="tab8">Ventas x Mes</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="row" id="graficos" name="graficos" style="display:none;">
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title" id="titulo_grafico" name="titulo_grafico"></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="table-responsive" id="tablaconsulta">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                
                
                    <div class="col-lg-12" style="display:none;">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Pie</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="resultadosPie1 text-center" style="width: 100%;">
                                        <canvas id="graficoPie1"></canvas>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-12">
                        <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Pie</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12 text-center" style="width: 100%;text-align:center;">
                                    <div class="resultadosPie12">
                                        <div id="piechart_3d1" style="width: 900px; height: 500px;"></div>
                                        <input type="hidden" name="base64pie1" id="base64pie1">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
    
                    <div class="col-lg-12 text-center">
                        <button type='button' id='btnpdf1' name='btnpdf1' class='btn btn-success' title='Reporte' onclick="btnpdf(1)"><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_2">
                <div class="row" id="graficos1" name="graficos1" style="display:none;">
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title" id="titulo_grafico1" name="titulo_grafico1"></h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="table-responsive" id="tablaconsultadinero">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                
                
                    <div class="col-lg-6 col-md-offset-3" style="display:none;">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Pie</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="resultadosPie2 text-center" style="width: 100%;">
                                        <canvas id="graficoPie2"></canvas>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Pie</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="resultadosPie12" style="width: 100%;text-align:center;">
                                        <div class="text-center" id="piechart_3d2" style="width: 900px; height: 500px;"></div>
                                        <input type="hidden" name="base64pie2" id="base64pie2">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-12 text-center">
                        <button type='button' id='btnpdf2' name='btnpdf2' class='btn btn-success' title='Reporte' onclick='btnpdf(2)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_3">
                <div class="row" id="graficos2" name="graficos2" style="display:none;">
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title" id="titulo_grafico2" name="titulo_grafico2"></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="table-responsive" id="tablaconsultaproducto">
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>                
                </div>
                <div class="col-lg-12 text-center">
                    <button type='button' id='btnpdf3' name='btnpdf3' class='btn btn-success' title='Reporte' onclick='btnpdf(3)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
                </div>
            </div>
            <div class="tab-pane" id="tab_4">
                <div class="row" id="grafbarra1" name="grafbarra1" style="display:none;">
                    <div class="col-lg-8 col-md-offset-2">
                    <!-- BARRA CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Barra Kg</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="resultadosBarra1 text-center" style="width: 100%;">
                                        <canvas id="graficoBarra1"></canvas>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-8 col-md-offset-2">
                    <!-- BARRA CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Barra $</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="resultadosBarra2 text-center" style="width: 100%;">
                                        <canvas id="graficoBarra2"></canvas>
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-8 col-md-offset-2" style="display:none;">
                    <!-- BARRA CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Barra $</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div id="chart_div11" style="width: 900px; height: 500px;"></div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-12 text-center">
                        <button type='button' id='btnpdf4' name='btnpdf4' class='btn btn-success' title='Reporte' onclick='btnpdf(4)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_5">
                <div class="row" id="graficosMC1" name="graficosMC1" style="display:none;">
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title" id="titulo_graficoMC1" name="titulo_graficoMC1"></h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="table-responsive" id="tablaMC">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                
                
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Pie</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="resultadosPie12" style="width: 100%;text-align:center;">
                                        <div class="text-center" id="piechart_3d3" style="width: 900px; height: 500px;"></div>
                                        <input type="hidden" name="base64pie3" id="base64pie3">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-12 text-center">
                        <button type='button' id='btnpdf5' name='btnpdf5' class='btn btn-success' title='Reporte' onclick='btnpdf(5)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab_6">
                <div class="row" id="graficosAP1" name="graficosAP1" style="display:none;">
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title" id="titulo_graficoAP1" name="titulo_graficoAP1"></h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="table-responsive" id="tablaAP">
                                    </div>
                                </div>
                                
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                
                    <!--
                    <div class="col-lg-12">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Pie</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="resultadosPie12" style="width: 100%;text-align:center;">
                                        <div class="text-center" id="piechart_3d3" style="width: 900px; height: 500px;"></div>
                                        <input type="hidden" name="base64pie3" id="base64pie3">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    -->
                    <div class="col-lg-12 text-center">
                        <button type='button' id='btnpdf6' name='btnpdf6' class='btn btn-success' title='Reporte' onclick='btnpdf(6)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
                
            </div>
            <div class="tab-pane" id="tab_7">
                <div class="row" id="margen" name="margen" style="display:none;">
                    <div class="col-lg-12">
                    <!-- DONUT CHART -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title" id="titulo_grafico2" name="titulo_grafico2"></h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-xs-12 col-sm-12">
                                    <div class="table-responsive" id="tablaconsultaproductomargen">

                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-lg-12 text-center">
                        <button type='button' id='btnpdf7' name='btnpdf7' class='btn btn-success' title='Reporte' onclick='btnpdf(7)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab_8">
                <div class="row" id="graficoVentasxMes" name="graficoVentasxMes" style="display:none;">
                    <div class="col-lg-8 col-md-offset-2">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Gráfico Pie</h3>
                        
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="graficoVentasxMes12" style="width: 100%;text-align:center;">
                                    <canvas id="graficoline1"></canvas>
                                    <input type="hidden" name="base64line1" id="base64line1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 text-center">
                        <button type='button' id='btnpdf8' name='btnpdf8' class='btn btn-success' title='Reporte' onclick='btnpdf(8)'><i class='glyphicon glyphicon-print'></i> Reporte</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>
</div>

@include('generales.buscarcliente')
@include('generales.modalpdf')
@endsection
