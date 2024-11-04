<?php

namespace App\Models\View\PetaObjekPajak;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PetaObjekPajak extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "view_peta_objek_pajak";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
