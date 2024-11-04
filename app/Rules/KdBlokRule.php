<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class KdBlokRule implements Rule
{
    protected $table;
    protected $column;
    protected $kdKecamatan;
    protected $kdKelurahan;

    public function __construct($table, $column, $kdKecamatan, $kdKelurahan)
    {
        $this->table = $table;
        $this->column = $column;
        $this->kdKecamatan = $kdKecamatan;
        $this->kdKelurahan = $kdKelurahan;
    }

    public function passes($attribute, $value)
    {
        // Ubah nilai value menjadi array jika belum
        $values = is_array($value) ? $value : [$value];

        // Periksa apakah setiap nilai ada dalam tabel data_blok
        foreach ($values as $kd_blok) {
            if (!DB::table($this->table)
                ->where($this->column, $kd_blok)
                ->where([
                    'KD_KECAMATAN' => $this->kdKecamatan,
                    'KD_KELURAHAN' => $this->kdKelurahan,
                ])
                ->exists()) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'The selected kd blok is invalid.';
    }
}
