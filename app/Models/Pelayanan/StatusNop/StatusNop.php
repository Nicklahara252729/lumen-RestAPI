<?php

namespace App\Models\Pelayanan\StatusNop;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusNop extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "status_nop";

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
        'uuid_status_nop' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_status_nop = (string) Str::orderedUuid();
            $item->uuid_user = authAttribute()['id'];
        });
    }
}
