<?php

namespace App\Models\PbbMinimal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PbbMinimal extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table name
     */

    protected $table = "pbb_minimal";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->KD_PROPINSI = globalAttribute()['kdProvinsi'];
            $item->KD_DATI2 = globalAttribute()['kdKota'];
        });
    }
}
