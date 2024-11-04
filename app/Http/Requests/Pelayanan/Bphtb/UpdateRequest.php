<?php

namespace App\Http\Requests\Pelayanan\Bphtb;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'uuid_jenis_perolehan' => 'required|exists:jenis_perolehan,uuid_jenis_perolehan',
            'nop'                  => 'required',
            'no_hp_wp_1'           => 'required|max:15',
            'nilai_transaksi'      => 'required',
            'nik'                  => 'required|max:25',
            'no_hp_wp_2'           => 'required|max:15',
            'nama_wp_2'            => 'required',
            'id_provinsi'          => 'required|exists:provinsi,id_provinsi',
            'id_kabupaten'         => 'required|exists:kabupaten,id_kabupaten',
            'id_kecamatan'         => 'required|exists:kecamatan,id_kecamatan',
            'id_kelurahan'         => 'required|exists:kelurahan,id_kelurahan',
            'alamat_wp_2'          => 'required',
            'ktp'                  => 'nullable',
            'foto_op'              => 'nullable',
            'sertifikat_tanah'     => 'nullable',
            'fc_sppt_thn_berjalan' => 'nullable',
            'fc_sk_jual_beli'      => 'nullable',
            'perjanjian_kredit'    => 'nullable',
            'surat_pernyataan'     => 'nullable',
            'fc_surat_kematian'    => 'nullable',
            'fc_sk_ahli_waris'     => 'nullable',
            'sp_ganti_rugi'        => 'nullable',
            'sk_bpn'               => 'nullable',
            'fc_sk_hibah_desa'     => 'nullable',
            'risalah_lelang'       => 'nullable',
            'updated_by'           => 'required|exists:users,uuid_user',
            'dph_ktp'              => 'nullable',
            'dph_nama'             => 'nullable',
            'dph_nomor'            => 'nullable',
            'dph_npwp'             => 'nullable',
            'dph_alamat'           => 'nullable',
            'no_sertifikat'        => 'nullable',
            'luas_tanah'           => 'required|integer',
            'luas_bangunan'        => 'required|integer',
            'luas_njop_tanah'      => 'required|integer',
            'luas_njop_bangunan'   => 'required|integer',
            'ket_peraturan'        => 'nullable',
        ];
    }

}
