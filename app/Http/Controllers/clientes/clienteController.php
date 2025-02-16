<?php

namespace App\Http\Controllers\clientes;

use App\Http\Controllers\Controller;
use App\Models\clientes\clienteModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class clienteController extends Controller
{
    public function index(){
        try {
            $cliente = clienteModel::all();
            return response()->json($cliente);
        } catch (Throwable $e) {
            return response()->json(["message" => "Error en la petición", "errors" => $e], 404);
        }
    }

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
