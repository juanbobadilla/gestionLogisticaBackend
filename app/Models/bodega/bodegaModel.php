<?php

namespace App\Models\bodega;

use Illuminate\Database\Eloquent\Model;

class bodegaModel extends Model
{
    protected $table = "bodega";
    protected $primaryKey  = "idBodega";
    public $timestamps = false;
}
