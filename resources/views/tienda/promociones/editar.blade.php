@extends('layouts.tienda')

@section('main-content')
    <div class="row">
        <div class="col-12 p-1">
            <h1 class=" text-center bg-dark text-white p-2 ">Promociones Vigentes</h1>
        </div>
        <br>
        <div class="nav">
            <a href="{{route('promociones.reg')}}" class="btn btn-success ms-auto m-2">Nueva Promoción</a>
        </div>
        <br>
           <table class="table table-bordered table-sm table-striped text-center">
              <thead>
                    <tr class="bg-primary bg-opacity-25 ">
                                <th scope="col"># ID</th>
                                <th scope="col">Centro</th>
                                <th scope="col">Producto</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Mes de Promoción</th>
                                <th scope="col">Fecha Registro</th>
                                <th scope="col"></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promociones as $promocion)
                                <tr>
                                    <th>{{ $promocion->id }}</th>
                                    <td>{{ $promocion->center_name }}</td>
                                    <td>{{ $promocion->product_name }}</td>
                                    <td>{{ $promocion->price }}</td>
                                    <td>
                                        <select name="month" class="form-control">
                                            <option>Enero</option>
                                            <option>Febrero</option>
                                            <option>Marzo</option>
                                            <option>Abril</option>
                                            <option>Mayo</option>
                                            <option>Junio</option>
                                            <option>Julio</option>
                                            <option>Agosto</option>
                                            <option>Septiembre</option>
                                            <option>Octubre</option>
                                            <option>Noviembre</option>
                                            <option>Diciembre</option></select></td>
                                    <td>{{ $promocion->created_at }}</td>
                                 
                                    <td>
                                        <form action="{{ route('promociones.update') }}" method="get">
                                        <button type="submit" class="btn btn-default btn-danger">Guardar</button>
                                        </form>
                                        <a class="btn btn-default btn-primary" href="{{route('promociones.index')}}" >Volver</a>
                                    </td>
                                </tr>
                                @endforeach   
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

@endsection