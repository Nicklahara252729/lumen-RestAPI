<?php

namespace App\Models\Pelayanan\Lspop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lspop extends Model
{
    use HasFactory;

    /**
     * The table name
     */

    protected $table = "lspop";

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
}
