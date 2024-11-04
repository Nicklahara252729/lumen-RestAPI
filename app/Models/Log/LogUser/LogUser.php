<?php

namespace App\Models\Log\LogUser;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogUser extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "log_user";

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
        'uuid_log_user' => 'string',
        'uuid_user' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            $item->uuid_log_user = (string) Str::orderedUuid();
        });
    }
}
