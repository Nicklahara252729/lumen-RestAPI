<?php

namespace App\Models\PembayaranSppt\PembatalanTransaksi;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembatalanTransaksi extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "pembatalan_transaksi";

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
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uuid_pembatalan_transaksi' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_pembatalan_transaksi = (string) Str::orderedUuid();
        });
    }
}
