<?php

namespace App\Http\Controllers\clientes;

use App\Http\Controllers\Controller;
use App\Models\clientes\clienteModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class clienteController extends Controller
{
    /**
     * Método para obtener todos los registros de clientes.
     * 
     * Obtiene todos los registros de la tabla "cliente" y los retorna en formato JSON. 
     * Si ocurre un error durante la solicitud, se captura la excepción y se retorna un mensaje de error con código 404.
     *
     * @return Retorna todos los clientes en formato JSON o un mensaje de error si ocurre un fallo.
     */
    public function index(){
        try {
            $cliente = clienteModel::all();
            return response()->json($cliente);
        } catch (Throwable $e) {
            return response()->json(["message" => "Error en la petición", "errors" => $e], 404);
        }
    }

    /**
     * Método para obtener los detalles de un cliente por su ID.
     * 
     * Busca un cliente en la base de datos utilizando el ID proporcionado. 
     * Si el cliente existe, retorna los datos en formato JSON. 
     * Si no se encuentra, devuelve un mensaje de error con código 404.
     *
     * @param int $idCliente El ID del cliente a buscar.
     * @return Retorna los datos del cliente en formato JSON o un mensaje de error si no se encuentra.
     */
    public function clientePorId($idCliente){
        try {
            $cliente = clienteModel::find($idCliente);
            if(!is_null($cliente)){
                return response()->json($cliente);
            }else{
                return response()->json(["message" => "El usuario no existe"], 404);
            }
        } catch (Throwable $e) {
            throw new \Exception('Error al procesar la solicitud: ' . $e->getMessage());
        }
        
    }

    /**
     * Método para guardar un nuevo cliente.
     * 
     * Valida los datos enviados a través de la solicitud y crea un nuevo cliente en la base de datos. 
     * Si la validación es exitosa, guarda el cliente y retorna un mensaje de éxito con los datos guardados. 
     * Si la validación falla, retorna un mensaje de error con código 422.
     *
     * @param  $request La solicitud que contiene los datos del cliente.
     * @return Retorna un mensaje de éxito con los datos guardados o un mensaje de error si la validación falla.
     */
    public function store(Request $request){
        try {
            
            $validacion = $request->validate([
                "nombreCompleto" => "required",
                "direccion" =>"required",
                "telefono"=> "required",
                "email"=>"required",
                "tipoCliente" => "required"
            ]);

            $post = new clienteModel();
            $post->nombreCompleto = $request->nombreCompleto;
            $post->direccion = $request->direccion;
            $post->telefono = $request->telefono;
            $post->email = $request->email;
            $post->tipoCliente = $request->tipoCliente;
            $post->save();
            return response()->json(["message" => "datos guardados exitosamente", "data" => $validacion]);

        } catch (ValidationException $v) {
            return response()->json(["message" => "Error en la validación", "errors" => $v->errors()], 422);
        }
        

        
    }

    /**
     * Método para actualizar los datos de un cliente existente.
     * 
     * Valida los datos enviados a través de la solicitud y, si el cliente con el ID proporcionado existe, 
     * actualiza sus datos en la base de datos. Si la validación falla, retorna un mensaje de error con código 422.
     * Si el cliente no se encuentra, lanza una excepción.
     *
     * @param  $request La solicitud que contiene los datos para actualizar el cliente.
     * @return Retorna un mensaje de éxito con los datos actualizados o un mensaje de error si la validación falla.
     */
    public function update(Request $request){
        try {
            
            $request->validate([
                "idCliente"=> "required",
                "nombreCompleto" => "required",
                "direccion" =>"required",
                "telefono"=> "required",
                "email"=>"required",
                "tipoCliente" => "required"
            ]);
            $cliente = new clienteModel();
            $usuario = $cliente->findOrFail($request->idCliente);
    
            $usuario->nombreCompleto = $request->nombreCompleto;
            $usuario->direccion = $request->direccion;
            $usuario->telefono = $request->telefono;
            $usuario->email = $request->email;
            $usuario->tipoCliente = $request->tipoCliente;
            $usuario->save();
            return response()->json(["message" => "datos guardados exitosamente", "data" => $usuario]);


        } catch (ValidationException $v) {
            return response()->json(["message" => "Error en la validación", "errors" => $v->errors()], 422);
        }
        
    }

    /**
     * Método para eliminar un cliente existente por su ID.
     * 
     * Valida que se proporcione un ID de cliente. Si el cliente con el ID dado existe, lo elimina de la base de datos 
     * y retorna un mensaje de éxito con los datos eliminados. Si no se encuentra el cliente, retorna un mensaje de error con código 422.
     * En caso de cualquier error durante el proceso, se captura la excepción y se retorna un mensaje con código 422.
     *
     * @param  $request La solicitud que contiene el ID del cliente a eliminar.
     * @return Retorna un mensaje de éxito si el cliente es eliminado o un mensaje de error si el cliente no existe o hay un fallo en el proceso.
     */
    public function delete(Request $request){
        
        try {
            
           $request->validate([
            "idCliente"=> "required",
        ]);

        $cliente = clienteModel::find($request->idCliente);
        
        if( !is_null($cliente)){
            $cliente->delete();
            return response()->json(["message" => "datos eliminados exitosamente", "data" => $cliente]);
        }else{
            return response()->json(["message" => "no existe el usuario"], 422);
        } 

        } catch (ValidationException $v) {
            return response()->json(["message" => "no existe el usuario", "errors" => $v->errors()], 422);
        }
        
    }
}
