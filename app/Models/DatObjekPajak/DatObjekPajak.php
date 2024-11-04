<?php

namespace App\Models\DatObjekPajak;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DatObjekPajak extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table name
     */

    protected $table = "dat_objek_pajak";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
