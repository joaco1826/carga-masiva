@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Productos</div>
                <a href="{{ url("/export") }}"><button class="btn btn-primary mt-4 ml-4">Exportar productos</button></a>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form id="frmExcel" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file">
                            <button class="btn btn-primary" type="submit">Cargar productos</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
