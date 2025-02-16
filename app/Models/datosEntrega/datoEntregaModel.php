<?php

namespace App\Models\datosEntrega;

use App\Models\productos\productoModel;
use Illuminate\Database\Eloquent\Model;

class datoEntregaModel extends Model
{
    protected $table = "datosentrega";
    protected $primaryKey  = "idDatoEntrega";
    protected $fillable = [
        "tipoProducto",
        "cantidadProducto",
        "idbodegaEntrega",
        "precioEnvio",
        "placa",
        "numeroGuia",
        "descuento",
        "idProducto"
    ];
    public $timestamps = false;
}
