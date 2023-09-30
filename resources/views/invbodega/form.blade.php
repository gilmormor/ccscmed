<div class="form-group">
    <label for="nombre" class="col-lg-3 control-label requerido">Nombre</label>
    <div class="col-lg-8">
        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required maxlength="100"/>
    </div>
</div>
<div class="form-group">
    <label for="nomabre" class="col-lg-3 control-label requerido">Nombre abreviado</label>
    <div class="col-lg-8">
        <input type="text" name="nomabre" id="nomabre" class="form-control" value="{{old('nomabre', $data->nomabre ?? '')}}" required maxlength="5"/>
    </div>
</div>

<div class="form-group">
    <label for="desc" class="col-lg-3 control-label requerido">Descripción</label>
    <div class="col-lg-8">
        <input type="text" name="desc" id="desc" class="form-control" value="{{old('desc', $data->desc ?? '')}}" required maxlength="300"/>
    </div>
</div>
<div class="form-group">
    <label for="sucursal_id" class="col-lg-3 control-label requerido">Sucursal</label>
    <div class="col-lg-8">
        <select name="sucursal_id" id="sucursal_id" class="form-control select2" required>
            <option value="">Seleccione...</option>
            @foreach($sucursales as $sucursal)
                <option
                    value="{{$sucursal->id}}"
                    @if (isset($data->sucursal_id) and ($data->sucursal_id==$sucursal->id))
                        {{'selected'}}
                    @endif
                >
                    {{$sucursal->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label for="categoriaprod_id" class="col-lg-3 control-label requerido" data-toggle='tooltip' title="Categoria Producto">CategoriaProd</label>
    <div class="col-lg-8">
        <select name="categoriaprod_id[]" id="categoriaprod_id" class="selectpicker form-control invmovmodulobodsal_id" data-live-search='true' multiple data-actions-box='true'>
            @foreach($categoriaprodsucs as $categoriaprodsuc)
                <option
                    value="{{$categoriaprodsuc->id}}"
                    {{is_array(old('categoriaprod_id')) ? (in_array($categoriaprodsuc->id, old('categoriaprod_id')) ? 'selected' : '') : (isset($data) ? ($data->categoriaprods->firstWhere('id', $categoriaprodsuc->id) ? 'selected' : '') : '')}}
                    >
                    {{$categoriaprodsuc->nombre}}
                </option>
            @endforeach
        </select>
    </div>
</div>


<div class="form-group">
    <label for="activo" class="col-lg-3 control-label requerido">Estatus</label>
    <div class="col-lg-8">
        <select name="activo" id="activo" class="form-control select2" required>
            <option value="">Seleccione...</option>
            <option
                value="1"
                @if (isset($data->activo) and ($data->activo==1))
                    {{'selected'}}
                @endif
            >
                Activo
            </option>
            <option
                value="0"
                @if (isset($data->activo) and ($data->activo==0))
                    {{'selected'}}
                @endif
            >
                Inactivo
            </option>
        </select>
    </div>
</div>
<div class="form-group">
    <label for="tipo" class="col-lg-3 control-label requerido">Tipo</label>
    <div class="col-lg-8">
        <select name="tipo" id="tipo" class="form-control select2" required>
            <option value="">Seleccione...</option>
            <option
                value="1"
                @if (isset($data->tipo) and ($data->tipo==1))
                    {{'selected'}}
                @endif
            >
                Picking
            </option>
            <option
                value="2"
                @if (isset($data->tipo) and ($data->tipo==2))
                    {{'selected'}}
                @endif
            >
                Bodega
            </option>
            <option
                value="3"
                @if (isset($data->tipo) and ($data->tipo==3))
                    {{'selected'}}
                @endif
            >
                Despacho
            </option>
            <option
                value="4"
                @if (isset($data->tipo) and ($data->tipo==4))
                    {{'selected'}}
                @endif
            >
                Scrap
            </option>
            <option
                value="5"
                @if (isset($data->tipo) and ($data->tipo==5))
                    {{'selected'}}
                @endif
            >
                Produccion
            </option>
            <option
                value="6"
                @if (isset($data->tipo) and ($data->tipo==6))
                    {{'selected'}}
                @endif
            >
                Transitoria Pesaje
            </option>

        </select>
    </div>
</div>

<div class="form-group">
    <label for="orden" class="col-lg-3 control-label requerido">Orden</label>
    <div class="col-lg-8">
        <input type="text" name="orden" id="orden" class="form-control numerico" value="{{old('orden', $data->orden ?? '')}}" required maxlength="1"/>
    </div>
</div>