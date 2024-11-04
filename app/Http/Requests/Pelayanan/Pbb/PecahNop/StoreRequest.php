<?php

namespace App\Http\Requests\Pelayanan\Pbb\PecahNop;

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
            
            /**
             * pelayanan
             */
            'nomor_pelayanan'           => 'required|unique:App\Models\Pelayanan\Pelayanan\Pelayanan,nomor_pelayanan',
            'nop'                       => 'required',
            'uuid_layanan'              => 'required|exists:layanan,uuid_layanan',
            'uuid_jenis_pelayanan'      => 'required|exists:jenis_layanan,uuid_jenis_layanan',

            /**
             * letak objek pajak
             */
            'op_no_persil'              => 'nullable',
            'op_blok'                   => 'nullable',
            'op_rt'                     => 'nullable',
            'op_rw'                     => 'nullable',
            'op_jalan'                  => 'required',

            /**
             * data subjek pajak
             */
            'sp_no_ktp'                 => 'required',
            'sp_nm'                     => 'required',
            'sp_jalan'                  => 'required',
            'sp_blok'                   => 'nullable',
            'sp_rt'                     => 'nullable',
            'sp_rw'                     => 'nullable',
            'sp_kelurahan'              => 'required',
            'sp_kota'                   => 'required',
            'sp_kd_pos'                 => 'nullable',
            'sp_npwp'                   => 'nullable',
            'sp_telp'                   => 'nullable',
            'sp_status_pekerjaan'       => 'required',

            /**
             * data bumi
             */
            'luas_bumi'                 => 'required',
            'kd_znt'                    => 'required',
            'jns_bumi'                  => 'required',

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
            'status_verifikasi' => (int)1
        ]);
    }
}
