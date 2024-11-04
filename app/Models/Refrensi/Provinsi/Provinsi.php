<?php

namespace App\Models\Refrensi\Provinsi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provinsi extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_propinsi";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

}
