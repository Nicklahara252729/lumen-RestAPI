<?php

namespace App\Models\Pelayanan\PelayananBphtb;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PelayananBphtb extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "pelayanan_bphtb";

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
    protected $hidden = ['updated_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid_pelayanan_bphtb' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_pelayanan_bphtb = (string) Str::orderedUuid();
        });
    }
}
