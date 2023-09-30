@extends("theme.$theme.layout")
@section('titulo')
Resetear Clave
@endsection

@section("scripts")
    <script src="{{autoVer("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="box box-danger">
            <!--<form action="{{route('password.email')}}" method="POST">-->
            <form action="{{route('enviar_enviarcorreopassusu')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <input type="submit" value="Enviar" class="btn btn-info">
            </form>
        </div>
    </div>
</div>
@endsection