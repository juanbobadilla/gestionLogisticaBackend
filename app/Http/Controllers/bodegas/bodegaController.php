<?php

namespace App\Http\Controllers\bodegas;

use App\Http\Controllers\Controller;
use App\Models\bodega\bodegaModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class bodegaController extends Controller
{
    /**
     * Método para obtener todos los registros de la tabla "bodega".
     * 
     * @return Retorna una respuesta en formato JSON con los datos de la bodega
     * o un mensaje de error en caso de fallo.
     */
    public function index(){
        try {
            $bodega = bodegaModel::all();
            return response()->json($bodega);
        } catch (Throwable $e) {
            return response()->json(["message" => "Error en la petición", "errors" => $e], 404);
        }
    }

    /**
     * Método para obtener los detalles de una bodega por su ID.
     * 
     * Busca una bodega en la base de datos utilizando el ID proporcionado. 
     * Si la bodega existe, retorna los datos en formato JSON. 
     * Si no se encuentra, devuelve un mensaje de error con código 404.
     * En caso de un error durante el proceso, lanza una excepción.
     *
     * @param int $id El ID de la bodega a buscar.
     * @return Retorna los datos de la bodega en formato JSON o un mensaje de error si no se encuentra.
     * @throws \Exception Si ocurre un error al procesar la solicitud.
     */
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

    /**
     * Método para guardar los datos de una nueva bodega.
     * 
     * Valida los datos enviados a través de la solicitud y crea una nueva bodega en la base de datos. 
     * Si la validación es exitosa, guarda la bodega y retorna un mensaje de éxito con los datos guardados. 
     * Si hay un error en la validación, devuelve un mensaje de error con código 422.
     *
     * @param $request La solicitud que contiene los datos de la bodega.
     * @return Retorna un mensaje de éxito con los datos guardados o un mensaje de error en caso de validación fallida.
     */
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

    /**
     * Método para actualizar los datos de una bodega existente.
     * 
     * Valida los datos enviados a través de la solicitud y, si la bodega con el ID proporcionado existe,
     * actualiza sus datos en la base de datos. Si la bodega no se encuentra, devuelve un mensaje de error con código 404.
     * Si la validación falla, retorna un mensaje de error con código 422.
     *
     * @param $request La solicitud que contiene los datos para actualizar la bodega.
     * @return Retorna un mensaje de éxito con los datos actualizados o un mensaje de error si la bodega no existe o la validación falla.
     */
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
    /**
     * Método para eliminar una bodega existente por su ID.
     * 
     * Valida que se proporcione un ID de bodega. Si la bodega con el ID dado existe, la elimina de la base de datos 
     * y retorna un mensaje de éxito con los datos eliminados. Si no se encuentra la bodega, retorna un mensaje de error con código 422.
     * En caso de cualquier otro error, retorna un mensaje genérico con código 404.
     *
     * @param $request La solicitud que contiene el ID de la bodega a eliminar.
     * @return Retorna un mensaje de éxito si la bodega es eliminada o un mensaje de error si la bodega no existe o hay un fallo en el proceso.
     */
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
