<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoFormRequest;
use App\Http\Requests\UserPostRequest;
use App\Models\Warehouse;
use App\Models\Image;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Promo;
use GuzzleHttp\RedirectMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class TiendaController extends Controller
{

   public function index()
   {
      return view('tienda.index');
   }

   public function indexAlmacen()
   {
      $almacen = Warehouse::latest()->paginate(15);
      return view('tienda.almacen', compact('almacen', $almacen));
   }

   public function indexProductos()
   {
      $productos = Product::all();

      // a cada uno de los productos se le añade
      // la propiedad almacen que refiere al warehouse_id registrado
      // no seas pendejo cabrón
      foreach ($productos as $producto) {
         $almacenProducto = DB::table('product_warehouse')
            ->select('warehouse_id')
            ->where('product_id', '=', $producto->id)
            ->get();
         $producto->almacen = $almacenProducto[0]->warehouse_id;
      }

      return view('tienda.productos.index', compact('productos'));
   }

   public function indexVentas()
   {
      $ventas = Sale::latest()->paginate(5);
      return view('tienda.ventas', compact('ventas', $ventas));
   }

   public function indexPromocion(){
      $promociones = Promo::all();
      
      return view('tienda.promociones',compact('promociones'));
   }

   public function nuevoProductoForm()
   {
      $almacenes = DB::table('warehouses')->count();
      return view('tienda.productos.create', compact('almacenes'));
   }

   // se encarga de guardar un nuevo producto en la bd 
   // @req : solicitud post de formulario productos.create
   public function registrarProducto(Request $req)
   {
      // se valida la solicitud de registro de nuevo producto
      $validated_data = $req->validate([
         'nombre' => ['required', 'string', 'max:100'],
         'desc' => ['required', 'string', 'max:255'],
         'precio' => 'numeric',
         'costo' => 'numeric',
         'almacen' => 'numeric',
         'stock' => 'numeric'
      ]);

      // se crea un nuevo producto con la solicitud validada
      $producto = new Product($validated_data);
      $producto->active = True;
      $producto->save();

      // actualizo la tabla de relaciones almacen-producto
      // para lo que ambas entidades ya deben existir en la bd
      DB::table('product_warehouse')->insert([
         'stock' => $req->input('stock'),
         'product_id' => $producto->id,
         'warehouse_id' => $req->input('almacen'),
         'created_at' => Date::now()
      ]);

      return redirect('/tienda/productos');
   }

   public function verProducto($id)
   {
      $producto = Product::find($id);
      return view('tienda.productos.show', compact('producto', $producto));
   }

   public function updateProductoForm(Request $req, $id)
   {
      // encontramos el producto con el id 
      $producto = Product::find($id);

      // actualizamos ese producto con las variables del formulario update 
      $producto->update($req->all());

      $almacenProducto = DB::table('product_warehouse')
         ->select('warehouse_id', 'stock')
         ->where('product_id', '=', $producto->id)
         ->get();
      $producto->almacen = $almacenProducto[0]->warehouse_id;
      $producto->stock = $almacenProducto[0]->stock;

      // respondemos con el mismo formulario, pero ahora los valores van a ser leidos desde el servidor
      return view('tienda.productos.update', compact('producto', $producto));
   }

   public function updateProducto(Request $req, $id)
   {
      // selecciona products en la db 
      DB::table('products')
         // donde está este id
         ->where('id',$id)
         // y actualiza COLUMNA => VALOR
         ->update([
            'nombre' => $req->input('nombre'),
            'desc'   => $req->input('desc'),
            'precio' => $req->input('precio'),
            'costo'  => $req->input('costo'),
            'updated_at' => Date::now()->toDateTimeString()
         ]);

      DB::table('product_warehouse')
         ->where('product_id',$id)
         ->update([
            'stock'        => $req->input('stock'),
            'product_id'   => $id,
            'warehouse_id' => $req->input('almacen')
         ]);

      return redirect()->back();
   }

   public function buscarProducto(Request $req)
   {
      // $busqueda = $req->input('search');

      // return view('tienda.productos.show', compact('busqueda'));
   }

   public function deleteProducto(Product $producto)
   {
      $producto->destroy($producto->id);
      return redirect()->route('productos');
   }

   //By Marisol Benitez
   public function newPromo(Request $request){
      $promo = new Promo();
      $promo->center_name = $request->input('center_name');
      $promo->product_name = $request->input('product_name');
      $promo->price = $request->input('price');
      $promo->month = $request->input('month');
      $promo->active=True;
      $promo->save();
      return redirect('tienda/promocion');
  }

  public function viewPromo(){
      $promo = Promo::select('center_name')->get();
      return redirect('tienda/promocion');
  }

  public function PromoForm()
  {
   return view('tienda.promociones.registrar');
  }

  public function editPromo(){
   $promociones = Promo::all();
      
   return view('tienda.promociones.editar',compact('promociones'));
  }

  public function updatePromo(Request $request, Promo $promocion){
      $promocion->fill($request->input())->saveOrFail();
      return redirect('tienda/promocion');
  }
  //By Marisol Benitez
  public function destroyPromo($id){
   $promocion = Promo::find($id);
   $promocion->delete();
   return redirect('tienda/promocion');
      
  }
}
