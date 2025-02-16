<?php

namespace App\Http\Controllers\bodegas;

use App\Http\Controllers\Controller;
use App\Models\bodega\bodegaModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class bodegaController extends Controller
{
    public function index(){
        try {
            $bodega = bodegaModel::all();
            return response()->json($bodega);
        } catch (Throwable $e) {
            return response()->json(["message" => "Error en la petición", "errors" => $e], 404);
        }
    }

    public function bodegaId($id){
        try {
            $bodega = bodegaModel::find($id);
            if(!is_null($bodega)){
                return response()->json($bodega);
            }else{
                return response()->json(["message" => "La bodega no existe"], 404);
            }
        } catch (Throwable $e) {
            throw new \Exception('Error al procesar la solicitud: ' . $e->getMessage());
        }
        
    }

    public function store(Request $request){
        try {
            
            $validacion = $request->validate([
                "nombreBodega" => "required",
                "ubicacion" =>"required",
                "capacidadRecepcion"=> "required",
                "tipo"=>"required"
            ]);

            $post = new bodegaModel();
            $post->nombreBodega = $request->nombreBodega;
            $post->ubicacion = $request->ubicacion;
            $post->capacidadRecepcion = $request->capacidadRecepcion;
            $post->tipo = $request->tipo;
            $post->save();
            return response()->json(["message" => "datos guardados exitosamente", "data" => $validacion]);

        } catch (ValidationException $v) {
            return response()->json(["message" => "Error en la validación", "errors" => $v->errors()], 422);
        }
        

        
    }

    public function update(Request $request){
        try {
            
            $validacion = $request->validate([
                "idBodega" => "required",
                "nombreBodega" => "required",
                "ubicacion" =>"required",
                "capacidadRecepcion"=> "required",
                "tipo"=>"required"
            ]);
            $bodega = new bodegaModel();
            $dato = $bodega->find($request->idBodega);
            
            if(!is_null($dato)){
                $dato->nombreBodega = $request->nombreBodega;
                $dato->ubicacion = $request->ubicacion;
                $dato->capacidadRecepcion = $request->capacidadRecepcion;
                $dato->tipo = $request->tipo;
                $dato->save();
                return response()->json(["message" => "datos guardados exitosamente", "data" => $dato]);
            } else {
                return response()->json(["message"=> "La bodega no existe", "errors"=> $validacion],404);
            }

        } catch (ValidationException $v) {
            return response()->json(["message" => "Error en la validación", "errors" => $validacion], 422);
        }
        
    }
    
    public function delete(Request $request){
        try { 
            $request->validate([
            "idBodega"=> "required",
        ]);

        $bodega = bodegaModel::find($request->idBodega);
        
        if( !is_null($bodega)){
            $bodega->delete();
            return response()->json(["message" => "datos eliminados exitosamente", "data" => $bodega]);
        }else{
            return response()->json(["message" => "no existe la bodega"], 422);
        } 

        } catch (Throwable $e) {
            return response()->json(["message" => "Error en la petición", "errors" => $e], 404);
        }
    }
}
