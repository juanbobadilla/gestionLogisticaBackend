<?php

namespace App\Models\clientes;

use Illuminate\Database\Eloquent\Model;

class clienteModel extends Model
{
    protected $table = "clientes";
    protected $primaryKey  = "idCliente";
    public $timestamps = false;
}
