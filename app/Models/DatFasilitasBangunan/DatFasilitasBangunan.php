<?php

namespace App\Models\DatFasilitasBangunan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DatFasilitasBangunan extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table name
     */

    protected $table = "dat_fasilitas_bangunan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
