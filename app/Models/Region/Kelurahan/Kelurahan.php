<?php

namespace App\Models\Region\Kelurahan;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelurahan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "kelurahan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
