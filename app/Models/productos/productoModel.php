<?php

namespace App\Models\productos;

use App\Models\datosEntrega\datoEntregaModel;
use Illuminate\Database\Eloquent\Model;

class productoModel extends Model
{
    protected $table = "producto";
    protected $primaryKey  = "idProducto";
    protected $fillable = [
        "nombreProducto",
        "descripcion",
        "idBodega",
        "idCliente",
        "fechaCreacion"
    ];
    public $timestamps = false;

    public function producto_to_datosEntrega(){
        return $this->hasMany(datoEntregaModel::class,'idProducto','idProducto');
    }
}
