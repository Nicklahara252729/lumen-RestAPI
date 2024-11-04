<?php

namespace App\Traits;

trait Calculation
{

    /**
     * njop bumi
     */
    public function njopBumi($nilaiPerM2Tanah, $luasTanah)
    {
        $return = ($nilaiPerM2Tanah * 1000) * $luasTanah;
        return $return;
    }

    /**
     * njop SPPT
     */
    public function njopSppt($njopBumi, $njopBangunanSppt)
    {
        $return = $njopBumi + $njopBangunanSppt;
        return $return;
    }

    /**
     * njoptkp
     */
    public function njoptkp($luasBangunan)
    {
        $return = $luasBangunan > 0 ? 10000000 : 0;
        return $return;
    }

    /**
     * PBB terhutang
     */
    public function pbbTerhutang($njopSppt, $njoptkpSppt)
    {
        $return = ($njopSppt - $njoptkpSppt) > 1000000000 ? ($njopSppt - $njoptkpSppt) * 0.002 : ($njopSppt - $njoptkpSppt) * 0.001;
        return $return;
    }

    /**
     * faktor pengurang
     */
    public function faktorPengurang()
    {
        return 0;
    }

    /**
     * PBB harus dibayar
     */
    public function pbbHarusDibayar($pbbTerhutang, $faktorPengurang)
    {
        $return = $pbbTerhutang - $faktorPengurang;
        return $return;
    }

    /**
     * faktor pengali
     */
    public function faktorPengali($njopBumi, $njopBangunanSppt)
    {
        $return = $this->njopSppt($njopBumi, $njopBangunanSppt) > 1000000000 ? 0.002 : 0.001;
        return $return;
    }

    /**
     * BPHTB terhutang
     */
    public function bphtbTerhutang($npopkp)
    {
        return round($npopkp * 0.05);
    }

    /**
     * denda PBB
     */
    public function dendaPbb($bulan, $tagihan, $tahun)
    {
        $persen = (int)$tahun >= 2024 ? 0.01 : 0.02;
        $bulan = $bulan > 24 ? 24 : ($bulan == 0 ? 0 : $bulan);
        $denda = $persen * $tagihan * $bulan;
        return round($denda);
    }

    /**
     * total skpdkb
     */
    public function totalSkpdkb($npopkp)
    {
        return 0.05 * $npopkp;
    }

    /**
     * NJOP PBB for BPHTB
     */
    public function njopPbbForBphtb($param)
    {
        $njopPbb    = $this->njopSppt($param['luas_njop_tanah'], $param['luas_njop_bangunan']);

        if ($param['kode_jenis_perolehan'] == 7) :
            $npop   = $njopPbb > $param['nilai_transaksi'] ? $param['nilai_transaksi'] : $njopPbb;
        else :
            $npop   = $njopPbb > $param['nilai_transaksi'] ? $njopPbb : $param['nilai_transaksi'];
        endif;

        $npoptkp    = is_null($param['npoptkp']) ? ($param['total'] >= 1 ? ($param['kode_jenis_perolehan'] == 5 ? $param['nilai'] : 0) : $param['nilai']) : $param['npoptkp'];
        $npopkp     = $npop - $npoptkp;
        $nilaiBphtb = $this->bphtbTerhutang($npopkp);
        $njopTanah  = $param['luas_njop_tanah'] == 0 ? 0 : $param['luas_njop_tanah'] / $param['luas_tanah'];
        $njopBangunan  = $param['luas_njop_bangunan'] == 0 ? 0 : $param['luas_njop_bangunan'] / $param['luas_bangunan'];

        return [
            'npop' => $npop,
            'npoptkp' => $npoptkp,
            'npopkp' => $npopkp <= 0 ? 0 : $npopkp,
            'nilaiBphtb' => $nilaiBphtb <= 0 ? 0 : $nilaiBphtb,
            'njopTanah' => $njopTanah,
            'njopBangunan' => $njopBangunan,
        ];
    }

    /**
     * NJOP PBB
     */
    public function njopPbb($param)
    {
        $njopTanahPerM  = $param['njop_bumi'] == 0 ? 0 : $param['njop_bumi'] / $param['luas_bumi_lama'];
        $njopBangunanPerM  = $param['luas_bangunan_lama'] == 0 ? 0 : $param['njop_bangunan'] / $param['luas_bangunan_lama'];
        $njopTanah = $njopTanahPerM * $param['luas_bumi_baru'];
        $njopBangunan = $njopBangunanPerM * $param['luas_bangunan_baru'];
        $njopSppt = $this->njopSppt($njopTanah, $njopBangunan);

        return [
            'njopTanah' => $njopTanah,
            'njopBangunan' => $njopBangunan,
            'njopSppt' => $njopSppt,
        ];
    }

    /**
     * pengurangan bphtb
     */
    public function penguranganBphtb($nilaiBphtb, $pengurangan)
    {
        $countPengurangan = ($nilaiBphtb * $pengurangan) / 100;
        $nilaiBphhtbPengurangan = $nilaiBphtb - $countPengurangan;
        return $nilaiBphhtbPengurangan;
    }

    /**
     * perhitungan bulan dendan PBB
     */
    public function perhitunganBulanDendaPBB($tglJatuhTempo)
    {
        $now = date('Y-m-d');
        $tempo = date('Y-m-d', strtotime($tglJatuhTempo));
        $tanggalTempo = strtotime($tempo);
        $tanggalSekarang = strtotime($now);
        $jumlahBulan = 0 + (date("Y", $tanggalSekarang) - date("Y", $tanggalTempo)) * 12;
        $jumlahBulan += date("m", $tanggalSekarang) - date("m", $tanggalTempo);
        $jumlahBulan = $jumlahBulan < 0 ? 0 : $jumlahBulan;
        return $jumlahBulan;
    }
}
