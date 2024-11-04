<?php

namespace App\Traits;

/**
 * import component
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * import models
 */

use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Sppt\Sppt;
use App\Models\User\User;
use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;
use App\Models\TagihanKolektor\TagihanKolektor;
use App\Models\PembayaranSppt\PembayaranManual\PembayaranManual;

trait Generator
{

    private function configGenerator()
    {
        $set = [
            'year' => Carbon::now()->format('Y'),
            'datetime' => Carbon::now()->toDateTimeLocalString(),
        ];
        return $set;
    }

    /**
     * nomor pelayanan pbb
     */
    public function nomorPelayananPbb()
    {
        $lastNomor = Pelayanan::select("id")
            ->orderByDesc("id")
            ->first();
        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->id ?? 0;
        $noBerikutnya = $noSebelumnya + 1;
        $noUrut = str_pad($noBerikutnya, 4, '0', STR_PAD_LEFT);
        return $this->configGenerator()['year'] . $noUrut;
    }


    /**
     * nomor pembayaran manual
     */
    public function nomorPembayaranManual()
    {
        $lastNomor = PembayaranManual::select("id")
            ->orderByDesc("id")
            ->first();
        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->id ?? 0;
        $noBerikutnya = $noSebelumnya + 1;
        $noUrut = str_pad($noBerikutnya, 4, '0', STR_PAD_LEFT);
        return $this->configGenerator()['year'] . $noUrut;
    }

    /**
     * nomor pelayanan bphtb
     */
    public function nomorRegistrasiBphtb()
    {
        $lastNomor = PelayananBphtb::select("id")
            ->orderByDesc("id")
            ->first();
        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->id ?? 0;
        $noBerikutnya = $noSebelumnya + 1;

        // Menggunakan str_pad untuk menambahkan nol di depan jika diperlukan
        $noUrut = str_pad($noBerikutnya, 4, '0', STR_PAD_LEFT);
        return '1276' . $this->configGenerator()['year'] . $noUrut;
    }

    /**
     * nomor urut sppt
     */
    public function noUrutSppt($kdKecamatan, $kdKelurahan, $kdBlok)
    {
        $lastNomor = Sppt::select("NO_URUT")
            ->where([
                'KD_KECAMATAN'   => $kdKecamatan,
                'KD_KELURAHAN'   => $kdKelurahan,
                'KD_BLOK'        => $kdBlok,
                //'THN_PAJAK_SPPT' => $this->configGenerator()['year']
            ])
            ->orderByDesc("NO_URUT")
            ->first();
        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->NO_URUT ?? 0;
        $noBerikutnya = $noSebelumnya + 1;

        // Menggunakan str_pad untuk menambahkan nol di depan jika diperlukan
        $noUrut = str_pad($noBerikutnya, 4, '0', STR_PAD_LEFT);
        return $noUrut;
    }

    /**
     * generate nop
     */
    public function nop($opKdKecamatan, $opKdKelurahan, $kdBlok, $noUrut, $statusKolektif)
    {
        $return = globalAttribute()['kdProvinsi'] . '.' . globalAttribute()['kdKota'] . '.' . $opKdKecamatan . '.' . $opKdKelurahan . '.' . $kdBlok . '.' . $noUrut . '.' . $statusKolektif;
        return $return;
    }

    /**
     * kode notaris
     */
    public function kodeNotaris()
    {
        $lastNomor = User::select("kode")
            ->where('role', 'notaris')
            ->orderByDesc("id")
            ->first();

        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->kode ?? 0;
        $noBerikutnya = $noSebelumnya + 1;

        // Menggunakan str_pad untuk menambahkan nol di depan jika diperlukan
        $return = str_pad($noBerikutnya, 3, '0', STR_PAD_LEFT);

        // Jika panjang nomor urut lebih besar dari 2, sesuaikan panjangnya sesuai kebutuhan
        // $return = str_pad($noBerikutnya, 3, '0', STR_PAD_LEFT); // untuk panjang 3, dst.

        return $return;
    }

    /**
     * no urut sts
     */
    public function noUrutSts()
    {
        $lastNomor = DB::connection('second_mysql')->table('STS_History')
            ->orderByDesc("kode")
            ->first();

        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->kode ?? 0;
        $noBerikutnya = $noSebelumnya + 1;

        // Menggunakan str_pad untuk menambahkan nol di depan jika diperlukan
        $nomor = str_pad($noBerikutnya, 7, '0', STR_PAD_LEFT);

        // Jika panjang nomor urut lebih besar dari 2, sesuaikan panjangnya sesuai kebutuhan
        // $return = str_pad($noBerikutnya, 3, '0', STR_PAD_LEFT); // untuk panjang 3, dst.

        return $nomor;
    }

    /**
     * no sts
     */
    public function noSts()
    {

        $noSts = "1259" . globalAttribute()['stsBphtb'] . $this->configGenerator()['year'] . $this->noUrutSts();

        return $noSts;
    }

    /**
     * nop sts bphtb
     */
    public function nopStsBphtb()
    {
        return $this->configGenerator()['year'] . $this->noUrutSts();
    }

    /**
     * nomor urut briva bphtb
     */
    public function nomorUrutBrivaBphtb($nop, $id = null)
    {
        $lastNomor = PelayananBphtb::select("id")
            ->where('nop', $nop)
            ->orderByDesc("id")
            ->first();
        $nomor = $id == null ? (is_null($lastNomor) ? 0 : (int)$lastNomor->id ?? 0) : $id;
        // $noBerikutnya = $noSebelumnya + 1;

        // Menggunakan str_pad untuk menambahkan nol di depan jika diperlukan
        $noUrut = str_pad($nomor, 9, '0', STR_PAD_LEFT);
        return $noUrut;
    }

    /**
     * nomor urut tagihan kolektor
     */
    public function nomorUrutTagihanKolektor()
    {
        $lastNomor = TagihanKolektor::select("nomor_tagihan")
            ->orderByDesc("id")
            ->first();
        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->nomor_tagihan ?? 0;
        $noBerikutnya = $noSebelumnya + 1;

        // Menggunakan str_pad untuk menambahkan nol di depan jika diperlukan
        $noUrut = str_pad($noBerikutnya, 4, '0', STR_PAD_LEFT);
        return $noUrut;
    }

    /**
     * kode bayar sppt
     */
    public function kodeBayar($noUrut)
    {
        $datetime = Carbon::parse($this->datetime)->locale('id');
        $datetime->settings(['formatFunction' => 'translatedFormat']);
        return $datetime->format('Ymd') . $noUrut;
    }

    /**
     * nomor skpdkb
     */
    public function noSkpdkb()
    {
        return $this->configGenerator()['year'] . rand(0, 999999);
    }

    /**
     * no sts pat
     */
    public function noStsPat()
    {
        $stsPat = globalAttribute()['stsPat'];
        $lastNomor = DB::connection('second_mysql')->table('STS_History')
            ->selectRaw(' MID(No_STS,11,11) AS sts')
            ->whereRaw('MID(No_STS,1,10) = "1259' . $stsPat . '"')
            ->orderByDesc("kode")
            ->first();

        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->sts ?? 0;
        $noBerikutnya = $noSebelumnya + 1;
        $return = str_pad($noBerikutnya, 11, '0', STR_PAD_LEFT);
        return $return;
    }

    /**
     * no sts reklame
     */
    public function noStsReklame()
    {
        $stsReklame = globalAttribute()['stsReklame'];
        $lastNomor = DB::connection('second_mysql')->table('STS_History')
            ->selectRaw('MID(No_STS,11,11) AS sts')
            ->whereRaw('MID(No_STS,1,10) = "1259' . $stsReklame . '"')
            ->orderByDesc("kode")
            ->first();

        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->sts ?? 0;
        $noBerikutnya = $noSebelumnya + 1;
        $return = str_pad($noBerikutnya, 11, '0', STR_PAD_LEFT);
        return $return;
    }

    /**
     * no sts restoran
     */
    public function noStsRestoran()
    {
        $lastNomor = DB::connection('third_mysql')->table('x_pengutip_restoran')
            ->selectRaw('MID(sptpd,7,11) AS no_sptpd')
            ->orderByDesc("id")
            ->first();

        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->no_sptpd ?? 0;
        $noBerikutnya = $noSebelumnya + 1;
        $return = str_pad($noBerikutnya, 5, '0', STR_PAD_LEFT);
        return $return;
    }

    /**
     * no urut regpribadi
     */
    public function noUrutRegpribadi()
    {
        $lastNomor = DB::connection('third_mysql')->table('x_regpribadi')
            ->selectRaw('regpribadi_id')
            ->orderByDesc("regpribadi_id")
            ->first();

        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->regpribadi_id ?? 0;
        $noBerikutnya = $noSebelumnya + 1;
        return $noBerikutnya;
    }

    /**
     * no urut PAD
     */
    public function noUrutNopd($jenis)
    {
        $getTable = DB::connection('third_mysql')->table('x_rekening')
            ->select('link')
            ->where('idrek', $jenis)
            ->first();
        if ($getTable->link == 'genset') :
            $table = 'x_penerangan';
            $row = 'penerangan_id';
        else :
            $table = 'x_' . $getTable->link;
            $row = $getTable->link . '_id';
        endif;
        $lastNomor = DB::connection('third_mysql')->table($table)
            ->selectRaw($row . ' AS id')
            ->orderByDesc($row)
            ->first();

        $noSebelumnya = is_null($lastNomor) ? 0 : (int)$lastNomor->id ?? 0;
        $noBerikutnya = $noSebelumnya + 1;
        return $noBerikutnya;
    }

    /**
     * generate NPWPD
     */
    public function npwpd($jenis, $kecamatan, $kelurahan)
    {
        $nourut = $this->noUrutRegpribadi();
        if (isset($kecamatan) && isset($kelurahan)) :
            $return = 'P' . $jenis . $nourut . $kelurahan . $kecamatan;
        else :
            $return = 'P' . $jenis . $nourut . '000000';
        endif;
        return $return;
    }

    /**
     * generate nopd
     */
    public function nopd($jenis)
    {
        $nourut = $this->noUrutNopd($jenis);
        $return = $jenis . '000' . $nourut;
        return $return;
    }
}
