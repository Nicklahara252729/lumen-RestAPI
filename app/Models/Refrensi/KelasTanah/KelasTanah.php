<?php

namespace App\Models\Refrensi\KelasTanah;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelasTanah extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "kelas_tanah";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
