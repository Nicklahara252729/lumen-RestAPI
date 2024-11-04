<?php

namespace App\Models\Refrensi\Jpb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jpb extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_jpb";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

}
