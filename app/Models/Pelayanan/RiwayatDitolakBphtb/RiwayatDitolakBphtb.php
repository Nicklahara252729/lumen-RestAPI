<?php

namespace App\Models\Pelayanan\RiwayatDitolakBphtb;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatDitolakBphtb extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "riwayat_ditolak_bphtb";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid_riwayat_ditolak' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_riwayat_ditolak = (string) Str::orderedUuid();
        });
    }
}
