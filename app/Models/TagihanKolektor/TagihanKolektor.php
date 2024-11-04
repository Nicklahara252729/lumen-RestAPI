<?php

namespace App\Models\TagihanKolektor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class TagihanKolektor extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table name
     */

    protected $table = "tagihan_kolektor";

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    protected $casts = [
        'uuid_tagihan_kolektor' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_tagihan_kolektor = (string) Str::orderedUuid();
        });
    }
}
