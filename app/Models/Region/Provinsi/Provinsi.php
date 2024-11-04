<?php

namespace App\Models\Region\Provinsi;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provinsi extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "provinsi";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
