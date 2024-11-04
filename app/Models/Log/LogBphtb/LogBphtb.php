<?php

namespace App\Models\Log\LogBphtb;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogBphtb extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "log_bphtb";

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
        'uuid_log_bphtb' => 'string',
        'uuid_user' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_log_bphtb = (string) Str::orderedUuid();
        });
    }
}
