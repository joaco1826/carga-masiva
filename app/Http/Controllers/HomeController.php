<?php

namespace App\Http\Controllers;

use App\SubCategoria;
use App\Category;
use App\Image;
use App\Label;
use App\Product;
use App\Size;
use App\SubSubCategoria;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function upload(Request $request)
    {
        request()->validate([
           "file" => "required|file"
        ]);

        $path = $request->file('file')->store('files');
        $data = Excel::load($path, function($reader) {})->get();
        $data->groupBy('firstname');
        if(!empty($data) && $data->count())
        {
            foreach ($data->toArray() as $key => $v)
            {
                $category = Category::where("name", $v["categoria"])->first();
                if (!$category)
                {
                    return response(json_encode(
                        ["message" => "La categoria " . $v["categoria"] . " no existe"]
                    ), 400)->header('Content-Type', 'text/json');
                }

                $subcategoria = SubCategoria::where("name", $v["subcategoria"])->first();
                if (!$subcategoria)
                {
                    return response(json_encode(
                        ["message" => "La subcategoria " . $v["subcategoria"] . " no existe"]
                    ), 400)->header('Content-Type', 'text/json');
                }

                if ($v["sub_subcategoria"] != "") {
                    $subsubcategoria = SubSubCategoria::where("name", $v["sub_subcategoria"])->first();
                    if (!$subsubcategoria)
                    {
                        return response(json_encode(
                            ["message" => "La subcategoria " . $v["sub_subcategoria"] . " no existe"]
                        ), 400)->header('Content-Type', 'text/json');
                    }
                    else
                    {
                        $subsubcategoria_id = $subsubcategoria->id;
                    }
                } else {
                    $subsubcategoria_id = NULL;
                }


                $product = Product::find($v["id"]);
                if ($product)
                {
                    $product->update([
                        "name" => $v["nombre"],
                        "price" => $v["precio"],
                        "discount" => $v["descuento"],
                        "img" => $v["imagen"],
                        "categorias_id" => $category->id,
                        "subcategorias_id" => $subcategoria->id,
                        "sub_subcategorias_id" => $subsubcategoria_id,
                        "description" => $v["caracteristicas"],
                        "tipo" => $v["tipo"],
                        "stock" => $v["stock"],
                        "puntos" => $v["puntos"],
                        "video" => $v["video"],
                        "plataforma" => $v["plataforma"],
                        "editorial" => $v["editorial"],
                        "desarrollador" => $v["desarrollador"],
                        "nuevo" => mb_strtoupper($v["nuevo"]),
                        "fecha_lanzamiento" => $v["fecha_lanzamiento"]
                    ]);
                } else {
                    $product = Product::create([
                        "name" => $v["nombre"],
                        "price" => $v["precio"],
                        "discount" => $v["descuento"],
                        "img" => $v["imagen"],
                        "categorias_id" => $category->id,
                        "subcategorias_id" => $subcategoria->id,
                        "sub_subcategorias_id" => $subsubcategoria_id,
                        "description" => $v["caracteristicas"],
                        "tipo" => $v["tipo"],
                        "stock" => $v["stock"],
                        "puntos" => $v["puntos"],
                        "video" => $v["video"],
                        "plataforma" => $v["plataforma"],
                        "editorial" => $v["editorial"],
                        "desarrollador" => $v["desarrollador"],
                        "nuevo" => mb_strtoupper($v["nuevo"]),
                        "fecha_lanzamiento" => $v["fecha_lanzamiento"]
                    ]);
                }
            }

            return response(json_encode(
                ["message" => "El archivo fue subido exitosamente"]
            ), 200)->header('Content-Type', 'text/json');

        } else {
            return response(json_encode(
                ["message" => "El archivo está vacío"]
            ), 400)->header('Content-Type', 'text/json');
        }

    }

    public function export()
    {
        Excel::create('Productos', function($excel) {

            $products = Product::all();

            $excel->sheet('Productos', function($sheet) use($products) {

                $sheet->row(1, [
                    'ID', 'NOMBRE', 'CATEGORIA', 'SUBCATEGORIA', 'SUB_SUBCATEGORIA', 'PRECIO', 'PUNTOS', 'DESCUENTO', 'IMAGEN',
                    'CARACTERISTICAS', 'TIPO', 'STOCK', 'VIDEO', 'PLATAFORMA', 'EDITORIAL', 'DESARROLLADOR', 'NUEVO',
                    'FECHA_LANZAMIENTO'
                ]);

                foreach($products as $index => $pro) {
                    $subsubcategoria = ($pro->sub_subcategoria) ? $pro->sub_subcategoria->name : "";
                    $sheet->row($index+2, [
                        $pro->id,
                        $pro->name,
                        $pro->category->name,
                        $pro->subcategoria->name,
                        $subsubcategoria,
                        $pro->precio,
                        $pro->puntos,
                        $pro->descuento,
                        $pro->img,
                        $pro->description,
                        $pro->type_pro,
                        $pro->stock,
                        $pro->video,
                        $pro->plataforma,
                        $pro->editorial,
                        $pro->desarrollador,
                        $pro->nuevo,
                        $pro->fecha_lanzamiento
                    ]);
                }

            });

        })->export('xlsx');
    }
}
