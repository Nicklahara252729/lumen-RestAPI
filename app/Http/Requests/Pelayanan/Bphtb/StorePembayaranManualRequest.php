<?php

namespace App\Http\Requests\Pelayanan\Bphtb;

use App\Http\Requests\FormRequest;
use App\Rules\NoSTS;

class StorePembayaranManualRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'No_STS'            => ['required', new NoSTS()],
            'Tahun'             => 'required',
            'Tgl_STS'           => 'required',
            'No_NOP'            => 'required',
            'No_Pokok_WP'       => 'required',
            'Nama_Pemilik'      => 'required',
            'Alamat_Pemilik'    => 'required',
            'Nilai'             => 'required',
            'Tgl_Bayar'         => 'required',
            'Kode_Pengesahan'   => 'required',
        ];
    }
}
