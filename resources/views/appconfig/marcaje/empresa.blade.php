@extends("theme.$theme.layout")
@section('titulo') Config. Perímetro — {{ $empresa->emp_nombre }} @endsection

@section('styles')
<style>
#mapa { height: 380px; border-radius: 6px; border: 1px solid #ddd; }
.coord-box { font-family: monospace; background: #f9f9f9; }
</style>
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/general.js")}}" type="text/javascript"></script>
    <script>
    // Sincronizar campos lat/lng cuando el usuario escribe
    document.addEventListener('DOMContentLoaded', function() {
        var lat  = document.getElementById('marc_lat');
        var lng  = document.getElementById('marc_lng');
        var radio= document.getElementById('marc_radio');

        // Botón para obtener ubicación actual del navegador
        document.getElementById('btnUbicacion').addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Tu navegador no soporta geolocalización.');
                return;
            }
            this.disabled = true;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Obteniendo...';
            var btn = this;
            navigator.geolocation.getCurrentPosition(function(pos) {
                lat.value = pos.coords.latitude.toFixed(8);
                lng.value = pos.coords.longitude.toFixed(8);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-crosshairs"></i> Usar mi ubicación actual';
                actualizarResumen();
            }, function() {
                alert('No se pudo obtener la ubicación. Verifica los permisos del navegador.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-crosshairs"></i> Usar mi ubicación actual';
            });
        });

        [lat, lng, radio].forEach(function(el) {
            el.addEventListener('input', actualizarResumen);
        });

        actualizarResumen();
    });

    function actualizarResumen() {
        var lat   = parseFloat(document.getElementById('marc_lat').value)  || 0;
        var lng   = parseFloat(document.getElementById('marc_lng').value)  || 0;
        var radio = parseInt(document.getElementById('marc_radio').value)  || 0;
        var tol   = parseInt(document.getElementById('marc_tolerancia').value) || 0;
        var total = radio + tol;

        document.getElementById('resumen').innerHTML =
            'Radio efectivo: <strong>' + radio + ' m</strong> + tolerancia <strong>' + tol + ' m</strong> = <strong class="text-primary">' + total + ' m</strong>';

        if (lat && lng) {
            var url = 'https://www.openstreetmap.org/?mlat=' + lat + '&mlon=' + lng + '#map=17/' + lat + '/' + lng;
            document.getElementById('linkMapa').href = url;
            document.getElementById('linkMapa').style.display = '';
        }
    }
    </script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-8 col-md-10">
        @include('includes.mensaje')
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-map-marker"></i>
                    Perímetro de Marcaje — {{ $empresa->emp_nombre }}
                </h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('appconfig.marcaje') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <form action="{{ route('appconfig.marcaje.empresa.update', $empresa->emp_codh) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="box-body">

                    {{-- Activar validación --}}
                    <div class="form-group">
                        <label>Validación de perímetro</label>
                        <select name="marc_valida_perimetro" class="form-control" style="width:auto">
                            <option value="0" {{ !$empresa->marc_valida_perimetro ? 'selected' : '' }}>
                                Desactivada (permite marcar desde cualquier lugar)
                            </option>
                            <option value="1" {{ $empresa->marc_valida_perimetro ? 'selected' : '' }}>
                                Activada (solo dentro del perímetro)
                            </option>
                        </select>
                    </div>

                    <hr>
                    <h4>Centro del perímetro</h4>

                    <div class="form-group">
                        <button type="button" id="btnUbicacion" class="btn btn-default btn-sm">
                            <i class="fa fa-crosshairs"></i> Usar mi ubicación actual
                        </button>
                        <small class="text-muted"> — o ingresa las coordenadas manualmente</small>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('marc_lat') ? 'has-error' : '' }}">
                                <label>Latitud</label>
                                <input type="number" name="marc_lat" id="marc_lat"
                                       class="form-control coord-box"
                                       step="0.00000001" min="-90" max="90"
                                       value="{{ old('marc_lat', $empresa->marc_lat) }}"
                                       placeholder="Ej: 7.77004200">
                                @if($errors->has('marc_lat'))
                                    <span class="help-block">{{ $errors->first('marc_lat') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('marc_lng') ? 'has-error' : '' }}">
                                <label>Longitud</label>
                                <input type="number" name="marc_lng" id="marc_lng"
                                       class="form-control coord-box"
                                       step="0.00000001" min="-180" max="180"
                                       value="{{ old('marc_lng', $empresa->marc_lng) }}"
                                       placeholder="Ej: -72.22570100">
                                @if($errors->has('marc_lng'))
                                    <span class="help-block">{{ $errors->first('marc_lng') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <a id="linkMapa" href="#" target="_blank" style="display:none">
                            <i class="fa fa-map"></i> Ver en OpenStreetMap
                        </a>
                    </div>

                    <hr>
                    <h4>Distancias</h4>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('marc_radio') ? 'has-error' : '' }}">
                                <label>Radio del perímetro (metros)</label>
                                <input type="number" name="marc_radio" id="marc_radio"
                                       class="form-control" min="10" max="5000"
                                       value="{{ old('marc_radio', $empresa->marc_radio ?? 100) }}">
                                <span class="help-block">Distancia máxima permitida desde el centro.</span>
                                @if($errors->has('marc_radio'))
                                    <span class="help-block text-danger">{{ $errors->first('marc_radio') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group {{ $errors->has('marc_tolerancia') ? 'has-error' : '' }}">
                                <label>Tolerancia (metros)</label>
                                <input type="number" name="marc_tolerancia" id="marc_tolerancia"
                                       class="form-control" min="0" max="500"
                                       value="{{ old('marc_tolerancia', $empresa->marc_tolerancia ?? 50) }}">
                                <span class="help-block">Margen extra permitido por imprecisión del GPS.</span>
                                @if($errors->has('marc_tolerancia'))
                                    <span class="help-block text-danger">{{ $errors->first('marc_tolerancia') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" id="resumen"></div>

                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar configuración
                    </button>
                    <a href="{{ route('appconfig.marcaje') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
