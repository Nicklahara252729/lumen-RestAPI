<?php

namespace App\Models\Pelayanan\PetaObjekPajak;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PetaObjekPajak extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "peta_objek_pajak";

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
        'uuid_peta_objek_pajak' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_peta_objek_pajak = (string) Str::orderedUuid();
            $item->uuid_user = authAttribute()['id'];
        });
    }
}
