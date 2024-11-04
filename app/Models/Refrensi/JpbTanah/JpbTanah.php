<?php

namespace App\Models\Refrensi\JpbTanah;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class JpbTanah extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "ref_jpb_tanah";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
    protected $casts = [
        'uuid_ref_jpb_tanah' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_ref_jpb_tanah = (string) Str::orderedUuid();
        });
    }
}
