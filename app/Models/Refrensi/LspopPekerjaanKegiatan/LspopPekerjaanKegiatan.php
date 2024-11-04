<?php

namespace App\Models\Refrensi\LspopPekerjaanKegiatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LspopPekerjaanKegiatan extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_lspop_pekerjaan_kegiatan";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

}
