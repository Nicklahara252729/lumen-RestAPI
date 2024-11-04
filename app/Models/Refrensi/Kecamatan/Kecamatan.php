<?php

namespace App\Models\Refrensi\Kecamatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kecamatan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_kecamatan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

}
