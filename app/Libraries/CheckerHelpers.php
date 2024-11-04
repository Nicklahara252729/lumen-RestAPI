<?php

namespace App\Libraries;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * import models
 */

use App\Models\User\User;
use App\Models\Bidang\Bidang\Bidang;
use App\Models\Bidang\SubBidang\SubBidang;
use App\Models\Setting\Setting\Setting;
use App\Models\Setting\Slider\Slider;
use App\Models\Layanan\Layanan\Layanan;
use App\Models\Layanan\JenisLayanan\JenisLayanan;
use App\Models\Region\Provinsi\Provinsi;
use App\Models\Region\Kabupaten\Kabupaten;
use App\Models\Region\Kecamatan\Kecamatan;
use App\Models\Region\Kelurahan\Kelurahan;
use App\Models\Refrensi\Pekerjaan\Pekerjaan;
use App\Models\Refrensi\PetaBlok\PetaBlok;
use App\Models\Refrensi\KelasTanah\KelasTanah;
use App\Models\Refrensi\KelasBangunan\KelasBangunan;
use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Setting\Menu\Menu;
use App\Models\Akses\Akses;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\DatSubjekPajak\DatSubjekPajak;
use App\Models\PbbMinimal\PbbMinimal;
use App\Models\Sppt\Sppt;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\JenisPerolehan\JenisPerolehan;
use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;
use App\Models\MasterData\Npoptkp\Npoptkp;
use App\Models\Pelayanan\Lspop\Lspop;
use App\Models\Refrensi\LspopPekerjaan\LspopPekerjaan;
use App\Models\DatOpBumi\DatOpBumi;
use App\Models\DatOpBangunan\DatOpBangunan;

class CheckerHelpers
{

    /**
     * user checker
     */
    public function userChecker($data)
    {
        return User::where($data)->first();
    }

    /**
     * bidang checker
     */
    public function bidangChecker($data)
    {
        return Bidang::where($data)->first();
    }

    /**
     * sub bidang checker
     */
    public function subBidangChecker($data)
    {
        return SubBidang::where($data)->first();
    }

    /**
     * bidang check in sub bidang
     */
    public function bidangInSubBidangChecker($param)
    {
        $isUuid = Str::isUuid($param);
        if ($isUuid == true) :
            $data = SubBidang::where(["uuid_bidang" => $param])->get();
        else :
            $data = SubBidang::select("uuid_sub_bidang", "nama_sub_bidang", "sub_bidang.uuid_bidang")
                ->join("bidang", "bidang.uuid_bidang", "=", "sub_bidang.uuid_bidang")
                ->where(["nama_bidang" => $param])
                ->get();
        endif;
        return $data;
    }

    /**
     * setting checker
     */
    public function settingChecker($param)
    {
        $data = Str::isUuid($param) == true ? ["uuid_setting" => $param] : ["category" => $param];
        return Setting::where($data)->first();
    }

    /**
     * slider checker
     */
    public function sliderChecker($data)
    {
        return Slider::where($data)->first();
    }

    /**
     * user join bidang & sub bidang checker
     */
    public function userJoinBidangSubBidangChecker($param)
    {
        return User::select("users.*", "nama_bidang", "nama_sub_bidang", DB::raw('CASE WHEN profile_photo_path IS NULL THEN NULL
        ELSE CONCAT("' . url(path('user')) . '/", profile_photo_path) END AS profile_photo_path'),)
            ->leftJoin("bidang", "bidang.uuid_bidang", "=", "users.uuid_bidang")
            ->leftJoin("sub_bidang", "sub_bidang.uuid_sub_bidang", "=", "users.uuid_sub_bidang")
            ->where('uuid_user', $param)
            ->orWhere('nama_bidang', $param)
            ->orWhere('nama_sub_bidang', $param)
            ->orWhere('role', $param)
            ->get();
    }

    /**
     * user email & username checker
     */
    public function userEmailUsernameChecker($data)
    {
        return User::where(['email' => $data['email']])
            ->orWhere(['username' => $data['username']])
            ->first();
    }

    /**
     * layanan
     */
    public function layananChecker($data)
    {
        return layanan::select(DB::raw("uuid_layanan, layanan, status, CONCAT('" . url(path('layanan')) . "/', icon) AS icon"))
            ->where($data)
            ->first();
    }

    /**
     * jenis layanan
     */
    public function jenisLayananChecker($data)
    {
        return JenisLayanan::where($data)->first();
    }

    /**
     * provinsi
     */
    public function provinsiChecker($data)
    {
        $countArray = sizeof($data);
        if ($countArray > 1) :
            $response = Provinsi::where($data[0])
                ->orWhere($data[1])
                ->first();
        else :
            $response = Provinsi::where($data)->first();
        endif;
        return $response;
    }

    /**
     * kabupaten
     */
    public function kabupatenChecker($data)
    {
        $countArray = sizeof($data);
        if ($countArray > 1) :
            $response = Kabupaten::where($data[0])
                ->orWhere($data[1])
                ->first();
        else :
            $response = Kabupaten::where($data)->first();
        endif;
        return $response;
    }

    /**
     * kecamatan
     */
    public function kecamatanChecker($data)
    {
        $countArray = sizeof($data);
        if ($countArray > 1) :
            $response = Kecamatan::where($data[0])
                ->orWhere($data[1])
                ->first();
        else :
            $response = Kecamatan::where($data)->first();
        endif;
        return $response;
    }

    /**
     * kelurahan
     */
    public function kelurahanChecker($data)
    {
        $countArray = sizeof($data);
        if ($countArray > 1) :
            $response = Kelurahan::where($data[0])
                ->orWhere($data[1])
                ->first();
        else :
            $response = Kelurahan::where($data)->first();
        endif;
        return $response;
    }

    /**
     * pekerjaan
     */
    public function pekerjaanChecker($data)
    {
        $countArray = sizeof($data);
        if ($countArray > 1) :
            $response = Pekerjaan::where($data[0])
                ->orWhere($data[1])
                ->first();
        else :
            $response = Pekerjaan::where($data)->first();
        endif;
        return $response;
    }

    /**
     * peta blok
     */
    public function petaBlokChecker($data)
    {
        return PetaBlok::where($data)->first();
    }

    /**
     * peta blok
     */
    public function pelayananChecker($data)
    {
        $countArray = sizeof($data);
        if ($countArray > 1) :
            $response = Pelayanan::where($data[0])
                ->orWhere($data[1])
                ->first();
        else :
            $response = Pelayanan::where($data)->first();
        endif;
        return $response;
    }

    /**
     * refrensi kecamatan
     */
    public function refrensiKecamatanChecker($data)
    {
        return DB::table('ref_kecamatan')
            ->where($data)
            ->first();
    }

    /**
     * refrensi kelurahan
     */
    public function refrensiKelurahanChecker($data)
    {
        return DB::table('ref_kelurahan')
            ->where($data)
            ->first();
    }

    /**
     * menu
     */
    public function menuChecker($data)
    {
        return Menu::where($data)->first();
    }

    /**
     * akses
     */
    public function aksesChecker($data)
    {
        return Akses::where($data)->first();
    }

    /**
     * pbb minimal
     */
    public function pbbMInimalChecker($data)
    {
        $data = array_merge([
            'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
            'KD_DATI2' => globalAttribute()['kdKota']
        ], $data);
        return PbbMinimal::where($data)->first();
    }

    /**
     * kelas bumi
     */
    public function kelasBumiChecker($nilai)
    {
        return KelasTanah::where('THN_AKHIR_KLS_TANAH', '9999')
            ->where('KD_KLS_TANAH', '<>', 'XXX')
            ->whereRaw('? BETWEEN NILAI_MIN_TANAH AND NILAI_MAX_TANAH', [$nilai])
            ->first();
    }

    /**
     * kelas bangunan
     */
    public function kelasBangunanChecker($nilai)
    {
        return KelasBangunan::where('THN_AKHIR_KLS_BNG', '9999')
            ->where('KD_KLS_BNG', '<>', 'XXX')
            ->whereRaw('? BETWEEN NILAI_MIN_BNG AND NILAI_MAX_BNG', [$nilai])
            ->first();
    }

    /**
     * sppt checker
     */
    public function spptChecker($data)
    {
        $data = array_merge([
            'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
            'KD_DATI2' => globalAttribute()['kdKota']
        ], $data);
        return Sppt::where($data)->orderByDesc('THN_PAJAK_SPPT')->first();
    }

    /**
     * data objek pajak checker
     */
    public function datObjekPajakChecker($data)
    {
        $data = array_merge([
            'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
            'KD_DATI2' => globalAttribute()['kdKota']
        ], $data);
        return DatObjekPajak::where($data)->first();
    }

    /**
     * data objek pajak checker
     */
    public function datSubjekPajakChecker($data)
    {
        return DatSubjekPajak::where($data)->orderByDesc('id')->first();
    }

    /**
     * pembayaran sppt checker
     */
    public function pembayaranSpptChecker($data)
    {
        return PembayaranSppt::where($data)->first();
    }

    /**
     * jenis perolehan
     */
    public function jenisPerolehanChecker($data)
    {
        return JenisPerolehan::where($data)->first();
    }

    /**
     * pelayanan bphtb
     */
    public function pelayananBphtbChecker($data)
    {
        $countArray = sizeof($data);
        if ($countArray > 1) :
            $response = PelayananBphtb::where($data[0])
                ->orWhere($data[1])
                ->first();
        else :
            $response = PelayananBphtb::where($data)->first();
        endif;
        return $response;
    }

    /**
     * npoptkp checker
     */
    public function npoptkpChecker($data)
    {
        return Npoptkp::where($data)->first();
    }

    /**
     * lspop checker
     */
    public function lspopChecker($data)
    {
        return Lspop::where($data)->first();
    }

    /**
     * lspop pekerjaan checker
     */
    public function lspopPekerjaanChecker($data)
    {
        return LspopPekerjaan::where($data)->first();
    }

    /**
     * dat op bumi checker
     */
    public function datOpBumiChecker($data)
    {
        return DatOpBumi::where($data)->first();
    }

    /**
     * dat op bangunan checker
     */
    public function datOpBangunanChecker($data)
    {
        return DatOpBangunan::where($data)->first();
    }
}
