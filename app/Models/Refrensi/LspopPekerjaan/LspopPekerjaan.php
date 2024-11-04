<?php

namespace App\Models\Refrensi\LspopPekerjaan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LspopPekerjaan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_lspop_pekerjaan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

}