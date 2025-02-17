<?php

namespace App\Http\Controllers\productos;

use App\Http\Controllers\Controller;
use App\Models\bodega\bodegaModel;
use App\Models\productos\productoModel;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\DB;


class productoController extends Controller
{

    /**
     * Obtiene la lista de todos los productos junto con sus datos de entrega.
     *
     * Este método consulta la base de datos y recupera todos los productos, 
     * incluyendo la información de entrega asociada a cada uno. 
     * 
     * Si no hay productos registrados, retorna un mensaje indicando que la lista está vacía.
     *
     * @return Retorna un JSON con la lista de productos 
     * y sus datos de entrega, o un mensaje de error si la consulta falla.
     *
     * @throws \Exception Si hay un error en la consulta, se captura y se retorna un mensaje de error.
     */
    public function getAllDatosEntrega(){
        try {
            // Obtener todos los productos con sus datos de entrega
            $productos = productoModel::with('producto_to_datosEntrega')->get();

            if ($productos->isEmpty()) {
                return response()->json(["message" => "No hay productos registrados"], 200);
            }

            return response()->json($productos);

        } catch (\Exception $e) {
            return response()->json(["error" => "Error al obtener los datos: " . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los datos de un producto específico junto con su información de entrega.
     *
     * Este método busca un producto en la base de datos utilizando su ID y recupera 
     * su información de entrega asociada. 
     * 
     * Si el producto no existe, retorna un mensaje de error con código 404.
     *
     * @param int $idProducto Identificador del producto a buscar.
     * @return JsonResponse Retorna un JSON con los datos del producto 
     * y su información de entrega, o un mensaje de error si no se encuentra.
     *
     * @throws \Exception Si hay un error en la consulta, se captura y se retorna un mensaje de error.
     */
    public function getDatosEntregaById($idProducto){
        try {
            // Buscar el producto por su ID con sus datos de entrega
            $producto = productoModel::with('producto_to_datosEntrega')->find($idProducto);

            if (!$producto) {
                return response()->json(["error" => "Producto no encontrado"], 404);
            }

            return response()->json(["message" => "Producto encontrado exitosamente", "data" => $producto]);

        } catch (\Exception $e) {
            return response()->json(["error" => "Error al obtener el producto: " . $e->getMessage()], 500);
        }
    }


    /**
     * Almacena los datos de entrega de un producto en la base de datos.
     *
     * Este método recibe los datos de un producto y su entrega a través de una solicitud HTTP,
     * valida la información, calcula descuentos según el tipo de bodega de entrega 
     * (terrestre o marítimo) y almacena los datos en las tablas correspondientes 
     * dentro de una transacción para garantizar la consistencia de la información.
     *
     * Si ocurre un error en cualquier parte del proceso, la transacción se revierte 
     * para evitar registros inconsistentes en la base de datos.
     *
     * @param Request $request Contiene los datos del producto y su entrega.
     * @return JsonResponse Retorna un mensaje de éxito y los datos guardados, 
     * o un mensaje de error si la transacción falla.
     *
     * @throws \Exception Si hay un error en la operación, la transacción se revierte y 
     * se devuelve un mensaje de error.
     */
    public function storeDatosEntrega(Request $request){
        try {
            DB::beginTransaction();

            $validacion = $request->validate([
                "nombreProducto" => "required",
                "descripcion" =>"required",
                "idBodega"=> "required",
                "idCliente"=>"required",
                "fechaCreacion" => "required",
                "tipoProducto"=> "required",
                "cantidadProducto"=> "required",
                "idbodegaEntrega"=> "required",
                "precioEnvio"=> "required",
                "placa"=> "required",
                "numeroGuia"=> "required",
                "descuento"=> "required"
            ]);
    
            $validarEntrega = bodegaModel::find($request->idbodegaEntrega);

            $placaTemporal = null;
            $descuento = 0;
            
            if(!is_null($validarEntrega)){
                switch ($validarEntrega->tipo) {
                    case 'terrestre':
                        $descuento = ($request->cantidadProducto * $request->precioEnvio) * 0.95; // Aplicar descuento 5% );
                        $placaTemporal = "abc123";
                        break;
                    case 'maritimo':
                        $descuento = ($request->cantidadProducto * $request->precioEnvio) * 0.97; // Aplicar descuento 3% );
                        $placaTemporal = "abc1234a";
                        break;
                }
            }
            $producto = new productoModel;
            $producto->nombreProducto = $request->nombreProducto;
            $producto->descripcion = $request->descripcion;
            $producto->idBodega = $request->idBodega;
            $producto->idCliente = $request->idCliente;
            $producto->fechaCreacion = $request->fechaCreacion;
            $producto->save();
            
            $productoCreado = $producto;
            $producto->producto_to_datosEntrega()->create(
                [
                    "tipoProducto" => $request->tipoProducto,
                    "cantidadProducto" => $request->cantidadProducto,
                    "idbodegaEntrega" => $request->idbodegaEntrega,
                    "precioEnvio" => $request->precioEnvio,
                    "placa" => $placaTemporal,
                    "numeroGuia" => $request->numeroGuia,
                    "descuento" => "precio normal: ". ($request->cantidadProducto * $request->precioEnvio) ." precio Descuento: " . $descuento,
                    "idProducto" => $productoCreado->idProducto
                ]
            );
            
            $datos = productoModel::where("idProducto",$productoCreado->idProducto)->With('producto_to_datosEntrega')->first();
            DB::commit();

            return response()->json(["message" => "datos guardados exitosamente", "data" => $datos]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["error" => "Error al procesar la solicitud: " . $e->getMessage()], 500);
        }
        
    }

    /**
     * Actualiza los datos de un producto y su entrega en la base de datos.
     *
     * Este método recibe los nuevos datos de un producto y su información de entrega 
     * a través de una solicitud HTTP. Primero, valida los datos, luego verifica si 
     * el producto y sus datos de entrega existen en la base de datos. 
     * 
     * Si la bodega de entrega es terrestre o marítima, se recalcula el descuento 
     * y se actualizan los registros en las tablas correspondientes.
     *
     * Se utiliza una transacción para garantizar la integridad de los datos, asegurando 
     * que si ocurre un error en cualquier parte del proceso, la actualización no se complete 
     * de manera parcial y se revierta a su estado original.
     *
     * @param Request $request Contiene los nuevos datos del producto y su entrega.
     * @param int $idProducto Identificador del producto a actualizar.
     * @return JsonResponse Retorna un mensaje de éxito y los datos actualizados, 
     * o un mensaje de error si la transacción falla.
     *
     * @throws \Exception Si hay un error en la operación, la transacción se revierte y 
     * se devuelve un mensaje de error.
     */
    public function updateDatosEntrega(Request $request, $idProducto){
        try {
            DB::beginTransaction();

            // Validación de los datos de entrada
            $validacion = $request->validate([
                "nombreProducto" => "required",
                "descripcion" => "required",
                "idBodega" => "required",
                "idCliente" => "required",
                "fechaCreacion" => "required",
                "tipoProducto" => "required",
                "cantidadProducto" => "required",
                "idbodegaEntrega" => "required",
                "precioEnvio" => "required",
                "placa" => "required",
                "numeroGuia" => "required",
                "descuento" => "required"
            ]);

            // Buscar el producto en la base de datos
            $producto = productoModel::find($idProducto);

            if (!$producto) {
                return response()->json(["error" => "Producto no encontrado"], 404);
            }

            // Buscar la información de entrega asociada
            $datosEntrega = $producto->producto_to_datosEntrega()->first();


            if (!$datosEntrega) {
                return response()->json(["error" => "Datos de entrega no encontrados"], 404);
            }

            // Verificar el tipo de bodega para aplicar descuentos
            $validarEntrega = bodegaModel::find($request->idbodegaEntrega);

            $placaTemporal = null;
            $descuento = 0;

            if (!is_null($validarEntrega)) {
                switch ($validarEntrega->tipo) {
                    case 'terrestre':
                        $descuento = ($request->cantidadProducto * $request->precioEnvio) * 0.95; // 5% descuento
                        $placaTemporal = "abc123";
                        break;
                    case 'maritimo':
                        $descuento = ($request->cantidadProducto * $request->precioEnvio) * 0.97; // 3% descuento
                        $placaTemporal = "abc1234a";
                        break;
                }
            }

            // Actualizar el producto
            $producto->update([
                "nombreProducto" => $request->nombreProducto,
                "descripcion" => $request->descripcion,
                "idBodega" => $request->idBodega,
                "idCliente" => $request->idCliente,
                "fechaCreacion" => $request->fechaCreacion
            ]);

            // Actualizar los datos de entrega
            $datosEntrega->update([
                "tipoProducto" => $request->tipoProducto,
                "cantidadProducto" => $request->cantidadProducto,
                "idbodegaEntrega" => $request->idbodegaEntrega,
                "precioEnvio" => $request->precioEnvio,
                "placa" => $placaTemporal,
                "numeroGuia" => $request->numeroGuia,
                "descuento" => "precio normal: " . ($request->cantidadProducto * $request->precioEnvio) . " precio Descuento: " . $descuento
            ]);

            DB::commit();

            return response()->json(["message" => "Datos actualizados exitosamente", "data" => $producto->load('producto_to_datosEntrega')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["error" => "Error al actualizar la información: " . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un producto y sus datos de entrega de la base de datos.
     *
     * Este método busca un producto por su ID y, si existe, elimina primero 
     * los datos de entrega asociados antes de eliminar el producto. 
     * 
     * Se utiliza una transacción para garantizar la consistencia de los datos, 
     * asegurando que si ocurre un error en cualquier parte del proceso, 
     * la eliminación no se realice de manera parcial y se revierta a su estado original.
     *
     * @param int $idProducto Identificador del producto a eliminar.
     * @return JsonResponse Retorna un mensaje de éxito si la eliminación 
     * fue exitosa, o un mensaje de error si la transacción falla.
     *
     * @throws \Exception Si hay un error en la operación, la transacción se revierte y 
     * se devuelve un mensaje de error.
     */
    public function deleteDatosEntrega($idProducto){
        try {
            DB::beginTransaction();

            // Buscar el producto en la base de datos
            $producto = productoModel::find($idProducto);

            if (!$producto) {
                return response()->json(["error" => "Producto no encontrado"], 404);
            }

            // Buscar el registro de datos de entrega
            $datosEntrega = $producto->producto_to_datosEntrega()->first();

            // Si existen datos de entrega, eliminarlos primero
            if ($datosEntrega) {
                $datosEntrega->delete();
            }

            // Eliminar el producto después de borrar los datos de entrega
            $producto->delete();

            DB::commit();

            return response()->json(["message" => "Producto y datos de entrega eliminados exitosamente"]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["error" => "Error al eliminar la información: " . $e->getMessage()], 500);
        }
    }


    public function getInforme(){

        try {
            $datos = DB::select('CALL consultaInforme()');
    
            return response()->json(["message" => "Datos obtenidos exitosamente", "data" => $datos]);
    
        } catch (\Exception $e) {
            return response()->json(["error" => "Error al ejecutar el procedimiento almacenado: " . $e->getMessage()], 500);
        }
    }


}
