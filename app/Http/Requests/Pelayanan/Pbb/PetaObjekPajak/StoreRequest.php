<?php

namespace App\Http\Requests\Pelayanan\Pbb\PetaObjekPajak;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nop'                       => 'required|integer|max_digits:25',
            'nama'                      => 'required',
            'tahun'                     => 'required|integer|max_digits:4',
            'alamat'                    => 'required',
            'kategori_nop'              => 'required|in:1,2,3,4,5',
            'photo_depan'               => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'latitude_depan'            => 'required',
            'longitude_depan'           => 'required',
            'photo_batas_tanah.*'       => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'latitude_batas_tanah.*'    => 'required',
            'longitude_batas_tanah.*'   => 'required',

            /**
             * data komponen material
             */
            'kondisi_umum'              => ['required', Rule::in(['sangat baik', 'baik', 'sedang', 'jelek'])],
            'md_dalam_jenis'            => ['required', Rule::in(['gypsum import', 'gypsum lokal', 'pas. dind 1/2 batu', 'triplek', 'plywood'])],
            'md_dalam'                  => ['required', Rule::in(['str', 'bsm'])],
            'md_luar'                   => ['required', Rule::in(['kaca', 'pas celcon', 'pas 1/2 batu', 'beton pracetak', 'seng', 'kayu'])],
            'md_luar_jml_lt'            => 'required',
            'pd_dalam_jenis'            => ['required', Rule::in(['kaca impor', 'wall paper', 'kaca lokal', 'granit impor', 'marmer impor', 'granit lokal', 'lokal', 'lokal keramik', 'cat', 'keramik std.'])],
            'pd_dalam_jml_lt'           => 'required',
            'pd_dalam'                  => ['required', Rule::in(['str', 'bsm'])],
            'pd_luar_jenis'             => ['required', Rule::in(['kaca impor', 'kaca lokal', 'granit impor', 'marmer impor', 'granit lokal', 'lokal', 'lokal keramik', 'cat', 'keramik std.'])],
            'pd_luar_jml_lt'            => 'required',
            'pd_luar'                   => ['required', Rule::in(['str', 'bsm'])],
            'langit_langit_jenis'       => ['required', Rule::in(['gypsum', 'akustik', 'triplex + cat', 'eternit'])],
            'langit_langit_jml_lt'      => 'required',
            'langit_langit'             => ['required', Rule::in(['str', 'bsm'])],
            'atap'                      => ['required', Rule::in(['pelat beton', 'genteng keramik', 'genteng press beton', 'asbes gelombang', 'seng gelombang', 'genteng sirap', 'genteng tanah liat'])],
            'penutup_lantai_jenis'      => ['required', Rule::in(['granit impor', 'marme import', 'marmer lokal', 'granit lokal', 'karpet import', 'keramik standar', 'vinil', 'karper lokal', 'lantai kayu', 'psa ubin abu-abu', 'teraso', 'semen'])],
            'penutup_lantai_jml_lt'     => 'required',
            'penutup_lantai'            => ['required', Rule::in(['str', 'bsm'])],

        ];
    }
}
