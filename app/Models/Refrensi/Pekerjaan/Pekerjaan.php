<?php

namespace App\Models\Refrensi\Pekerjaan;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pekerjaan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_pekerjaan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
