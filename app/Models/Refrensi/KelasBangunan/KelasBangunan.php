<?php

namespace App\Models\Refrensi\KelasBangunan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelasBangunan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "kelas_bangunan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
