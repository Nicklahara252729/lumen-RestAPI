<?php

namespace App\Http\Requests\Pelayanan\Pbb\Mutasi;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Nop;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nomor_pelayanan'       => 'required|unique:App\Models\Pelayanan\Pelayanan\Pelayanan,nomor_pelayanan',
            'uuid_layanan'          => 'required|exists:layanan,uuid_layanan',
            'uuid_jenis_pelayanan'  => 'required|exists:jenis_layanan,uuid_jenis_layanan',
            'nop'                   => ['required', 'integer', 'max_digits:25', new Nop('sppt', $this->tahun)],
            'tahun'                 => 'required|integer|max_digits:4',
            'jalan_op'              => 'required',
            'blok_op'               => 'nullable',
            'rt_op'                 => 'nullable',
            'rw_op'                 => 'nullable',
            'luas_bumi'             => 'required',
            'luas_bangunan'         => 'required',
            'njop_bumi'             => 'required',
            'njop_bangunan'         => 'required',
            'nama_wp'               => 'required',
            'jalan_wp'              => 'required',
            'kelurahan'             => 'required',
            'kota'                  => 'required',
            'kode_pos'              => 'required',
            'telp'                  => 'required',
            'npwp'                  => 'required',
            'status_pekerjaan'      => 'required',
            'ktp'                   => 'required',
            'rw_wp'                 => 'required',
            'rt_wp'                 => 'required',
            'blok_wp'               => 'required',
        ];
    }
}
