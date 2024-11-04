<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class NoUrut implements Rule
{
    protected $table;
    protected $column;
    protected $kdKecamatan;
    protected $kdKelurahan;
    protected $status;

    public function __construct($table, $column, $kdKecamatan, $kdKelurahan, $status)
    {
        $this->table = $table;
        $this->column = $column;
        $this->kdKecamatan = $kdKecamatan;
        $this->kdKelurahan = $kdKelurahan;
        $this->status = $status;
    }

    public function passes($attribute, $value)
    {
        // Ubah nilai value menjadi array jika belum
        $values = is_array($value) ? $value : [$value];

        // Periksa apakah setiap nilai ada dalam tabel data_blok
        foreach ($values as $no_urut) {
            if (!DB::table($this->table)
                ->where($this->column, $no_urut)
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
        return 'The selected no urut ' . $this->status . ' is invalid.';
    }
}
