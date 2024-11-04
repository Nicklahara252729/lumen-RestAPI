<?php

namespace App\Models\Region\Kecamatan;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kecamatan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "kecamatan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
