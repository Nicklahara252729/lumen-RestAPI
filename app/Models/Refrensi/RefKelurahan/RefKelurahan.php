<?php

namespace App\Models\Refrensi\RefKelurahan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefKelurahan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_kelurahan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
