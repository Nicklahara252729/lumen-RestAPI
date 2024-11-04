<?php

namespace App\Models\PembayaranSppt\PembayaranSppt;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembayaranSppt extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "pembayaran_sppt";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
