<?php

namespace App\Models\Region\Kabupaten;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kabupaten extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "kabupaten";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
