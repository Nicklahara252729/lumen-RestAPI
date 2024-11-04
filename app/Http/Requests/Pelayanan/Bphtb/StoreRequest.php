<?php

namespace App\Http\Requests\Pelayanan\Bphtb;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
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
            'created_by'           => 'required|exists:users,uuid_user',
            'dph_ktp'              => 'nullable',
            'dph_nama'             => 'nullable',
            'dph_nomor'            => 'nullable',
            'dph_npwp'             => 'nullable',
            'dph_alamat'           => 'nullable',
            'no_sertifikat'        => 'nullable',
            'dph_nik'              => 'required|max:25',
            'dph_id_provinsi'      => 'required|exists:provinsi,id_provinsi',
            'dph_id_kabupaten'     => 'required|exists:kabupaten,id_kabupaten',
            'dph_id_kecamatan'     => 'required|exists:kecamatan,id_kecamatan',
            'dph_id_kelurahan'     => 'required|exists:kelurahan,id_kelurahan',
            'luas_tanah'           => 'required|integer',
            'luas_bangunan'        => 'required|integer',
            'luas_njop_tanah'      => 'required|integer',
            'luas_njop_bangunan'   => 'required|integer',
            'ket_peraturan'        => 'nullable',
        ];
    }

    protected function prepareForValidation()
    {
        $this->req->merge([
            'status_verifikasi' => (int)0
        ]);
    }

    // public function messages()
    // {
    //     return [
    //         'ktp.uploaded' => 'KTP maksimal berukuran 5MB .',
    //         'foto_op.uploaded' => 'Foto op maksimal berukuran 5MB .',
    //         'sertifikat_tanah.uploaded' => 'Sertifikat tahan maksimal berukuran 5MB .',
    //         'fc_sppt_thn_berjalan.uploaded' => 'Fc SPPT tahun berjalan maksimal berukuran 5MB .',
    //         'fc_sk_jual_beli.uploaded' => 'Fc SK jual beli maksimal berukuran 5MB .',
    //         'perjanjian_kredit.uploaded' => 'Perjanjian kredit maksimal berukuran 5MB .',
    //         'surat_pernyataan.uploaded' => 'Surat pernyataan maksimal berukuran 5MB .',
    //         'fc_surat_kematian.uploaded' => 'Surat kematian maksimal berukuran 5MB .',
    //         'fc_sk_ahli_waris.uploaded' => 'Fc SK ahli waris maksimal berukuran 5MB .',
    //         'sp_ganti_rugi.uploaded' => 'Sp ganti rugi maksimal berukuran 5MB .',
    //         'sk_bpn.uploaded' => 'Sk BPN maksimal berukuran 5MB .',
    //         'fc_sk_hibah_desa.uploaded' => 'Fc SK hibah maksimal berukuran 5MB .',
    //         'risalah_lelang.uploaded' => 'Risalah lelang maksimal berukuran 5MB .',
    //     ];
    // }    
}
