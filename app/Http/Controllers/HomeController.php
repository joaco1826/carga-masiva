<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Label;
use App\Product;
use App\Size;
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

                $brand = Brand::where("name", $v["marca"])->first();
                if (!$brand)
                {
                    return response(json_encode(
                        ["message" => "La marca " . $v["marca"] . " no existe"]
                    ), 400)->header('Content-Type', 'text/json');
                }

                $product = Product::where("reference", $v["referencia"])->first();
                if ($product)
                {
                    $product->update([
                        "name" => $v["nombre"],
                        "price" => $v["precio"],
                        "discount" => $v["descuento"],
                        "image" => $v["imagen"],
                        "category_id" => $category->id,
                        "brand_id" => $brand->id,
                        "description" => $v["caracteristicas"],
                        "featured" => $v["destacado"],
                        "status" => $v["estado"]
                    ]);
                } else {
                    $product = Product::create([
                        "name" => $v["nombre"],
                        "reference" => $v["referencia"],
                        "price" => $v["precio"],
                        "discount" => $v["descuento"],
                        "image" => $v["imagen"],
                        "category_id" => $category->id,
                        "brand_id" => $brand->id,
                        "description" => $v["caracteristicas"],
                        "featured" => $v["destacado"],
                        "status" => $v["estado"]
                    ]);
                }
                $sizes = explode('-', $v["tallas"]);
                $size_data = Size::whereIn("name", $sizes)->get();
                $size_array = [];
                foreach ($size_data as $size) {
                    $size_array[] = $size->id;
                }
                $product->sizes()->sync($size_array);

                $labels = explode('-', $v["etiquetas"]);
                $label_data = Label::whereIn("name", $labels)->get();
                $label_array = [];
                foreach ($label_data as $label) {
                    $label_array[] = $label->id;
                }
                $product->labels()->sync($label_array);
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
}
