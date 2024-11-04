<?php

namespace App\Models\DatSubjekPajak;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DatSubjekPajak extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table name
     */

    protected $table = "dat_subjek_pajak";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
