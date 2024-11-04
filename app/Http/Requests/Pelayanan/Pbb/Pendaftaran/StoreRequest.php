<?php

namespace App\Http\Requests\Pelayanan\Pbb\Pendaftaran;

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
            'nomor_pelayanan'       => 'required|unique:App\Models\Pelayanan\Pelayanan\Pelayanan,nomor_pelayanan',
            'uuid_layanan'          => 'required|exists:layanan,uuid_layanan',

            /**
             * SPOP
             */
            'status_kolektif'       => ['required', Rule::in([0, 1, 7])],
            'uuid_jenis_pelayanan'  => 'required|exists:jenis_layanan,uuid_jenis_layanan',
            'id_pemohon'            => 'required|max:16',
            'nama_lengkap'          => 'required',
            'id_provinsi'           => 'required|exists:provinsi,id_provinsi',
            'id_kabupaten'          => 'required|exists:kabupaten,id_kabupaten',
            'id_kecamatan'          => 'required|exists:kecamatan,id_kecamatan',
            'id_kelurahan'          => 'required|exists:kelurahan,id_kelurahan',
            'alamat'                => 'required',

            /**
             * data subjek pajak
             */
            'sp_id_provinsi'        => 'exists:provinsi,id_provinsi',
            'sp_id_kabupaten'       => 'exists:kabupaten,id_kabupaten',
            'sp_id_kecamatan'       => 'exists:kecamatan,id_kecamatan',
            'sp_id_kelurahan'       => 'exists:kelurahan,id_kelurahan',
            'sp_nik'                => 'required|max:16',
            'sp_nama_lengkap'       => 'required',
            'sp_alamat'             => 'required',
            'sp_rt'                 => 'nullable',
            'sp_rw'                 => 'nullable',
            'sp_blok'               => 'nullable',
            'sp_kd_pos'             => 'nullable',
            'sp_no_hp'              => 'required|max:15',
            'sp_npwp'               => 'nullable|max:16',
            'sp_kd_pekerjaan'       => 'nullable|exists:ref_pekerjaan,kode',

            /**
             * data objek pajak
             */
            'op_kd_kecamatan'       => 'nullable|exists:ref_kecamatan,KD_KECAMATAN',
            'op_kd_kelurahan'       => 'nullable|exists:ref_kelurahan,KD_KELURAHAN',
            'op_kd_blok'            => 'nullable|exists:dat_peta_blok,KD_BLOK',
            'op_kelas_bumi'         => 'nullable',
            'op_jenis_tanah'        => 'nullable',
            'op_luas_tanah'         => 'nullable',
            'op_luas_bangunan'      => 'nullable',
            'op_blok'               => 'nullable',
            'op_rw'                 => 'nullable',
            'op_rt'                 => 'nullable',

            /**
             * data bumi
             */
            'kd_znt'                 => 'nullable',

            /**
             * dokumen kelengkapan
             */
            'fc_surat_tanah'        => 'nullable',
            'ktp_pemilik'           => 'nullable',
            'sppt_tetangga_sebelah' => 'nullable',
            'foto_objek_pajak'      => 'nullable',
            'spop'                  => 'nullable',
            'lsop'                  => 'nullable',
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'status_verifikasi' => (int)1
        ]);
    }
}
