<?php

namespace App\Http\Requests\Pelayanan\Pbb\Lspop;

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
            'nomor_pelayanan'           => 'required|unique:App\Models\Pelayanan\Pelayanan\Pelayanan,nomor_pelayanan',
            'uuid_layanan'              => 'required|exists:layanan,uuid_layanan',

            /**
             * SPOP
             */
            'status_kolektif'           => ['required', Rule::in([0, 1, 7])],
            'uuid_jenis_pelayanan'      => 'required|exists:jenis_layanan,uuid_jenis_layanan',
            'id_pemohon'                => 'required|max:16',
            'nama_lengkap'              => 'required',
            'id_provinsi'               => 'required|exists:provinsi,id_provinsi',
            'id_kabupaten'              => 'required|exists:kabupaten,id_kabupaten',
            'id_kecamatan'              => 'required|exists:kecamatan,id_kecamatan',
            'id_kelurahan'              => 'required|exists:kelurahan,id_kelurahan',
            'alamat'                    => 'required',

            /**
             * data subjek pajak
             */
            'nop'                       => ['required', 'integer', 'max_digits:25', new Nop('sppt')],
            'sp_nama_lengkap'           => 'required',
            'sp_alamat'                 => 'required',
            'op_alamat'                 => 'required',

            /**
             * LSPOP rincian data bangunan
             */
            'no_bangunan.*'             => 'nullable',
            'jenis_bangunan.*'          => 'nullable',
            'luas_bangunan.*'           => 'nullable',
            'thn_dibangun.*'            => 'nullable',
            'jlh_lantai.*'              => 'nullable',
            'thn_renovasi.*'            => 'nullable',
            'kondisi_bangunan.*'        => 'nullable',
            'konstruksi.*'              => 'nullable',
            'atap.*'                    => 'nullable',
            'dinding.*'                 => 'nullable',
            'lantai.*'                  => 'nullable',
            'langit_langit.*'           => 'nullable',

            /**
             * LSPOP fasilitas
             */
            'daya_listrik.*'            => 'nullable',
            'jumlah_ac_split.*'         => 'nullable',
            'jumlah_ac_window.*'        => 'nullable',
            'luas_kolam_renang.*'       => 'nullable',
            'finishing_kolam.*'         => 'nullable',
            'jlt_beton_dgn_lampu.*'     => 'nullable',
            'jlt_beton_tanpa_lampu.*'   => 'nullable',
            'jlt_aspal_dgn_lampu.*'     => 'nullable',
            'jlt_aspal_tanpa_lampu.*'   => 'nullable',
            'jlt_rumput_dgn_lampu.*'    => 'nullable',
            'jlt_rumput_tanpa_lampu.*'  => 'nullable',
            'panjang_pagar.*'           => 'nullable',
            'bahan_pagar.*'             => 'nullable',
            'jlh_pabx.*'                => 'nullable',
            'ac_sentral.*'              => 'nullable',
            'lph_ringan.*'              => 'nullable',
            'lph_sedang.*'              => 'nullable',
            'lph_berat.*'               => 'nullable',
            'lph_dgn_penutup_lantai.*'  => 'nullable',
            'jlh_lift_penumpang.*'      => 'nullable',
            'jlh_lift_kapsul.*'         => 'nullable',
            'jlh_lift_barang.*'         => 'nullable',
            'jlh_eskalator_1.*'         => 'nullable',
            'jlh_eskalator_2.*'         => 'nullable',
            'pemadam_hydrant.*'         => 'nullable',
            'pemadam_sprinkler.*'       => 'nullable',
            'pemadam_fire_alarm.*'      => 'nullable',
            'sumur_artesis.*'           => 'nullable',
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'status_verifikasi' => (int)1,
            'created_by'        => authAttribute()['id']
        ]);
    }
}
