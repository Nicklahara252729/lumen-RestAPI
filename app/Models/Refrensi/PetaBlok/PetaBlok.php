<?php

namespace App\Models\Refrensi\PetaBlok;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PetaBlok extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "dat_peta_blok";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * hidden attribute
     */
    protected $hidden = [
        'createdtime'
    ];
}
