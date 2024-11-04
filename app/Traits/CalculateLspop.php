<?php

namespace App\Traits;

/**
 * import component
 */

use Illuminate\Support\Carbon;

/**
 * import models
 */

use App\Models\Refrensi\LspopDbkbMaterial\LspopDbkbMaterial;
use App\Models\Refrensi\LspopDbkbStandard\LspopDbkbStandard;
use App\Models\Refrensi\LspopPenyusutan\LspopPenyusutan;
use App\Models\DatFasilitasBangunan\DatFasilitasBangunan;
use App\Models\Refrensi\LspopFasNonDep\LspopFasNonDep;

trait CalculateLspop
{
    /**
     * config environment value
     */
    private function config()
    {
        $set = [
            'year'     => Carbon::now()->format('Y'),
            'datetime' => Carbon::now()->toDateTimeLocalString(),
        ];
        return $set;
    }

    /**
     * hitung biaya material
     */
    private function hitungMaterail($setValue)
    {
        $material = [
            ['material' => 'atap', 'kode' => 23],
            ['material' => 'dinding', 'kode' => 21],
            ['material' => 'lantai', 'kode' => 22],
            ['material' => 'langit_langit', 'kode' => 24],
        ];

        $result = [];
        foreach ($material as $value) :
            $getMaterial = LspopDbkbMaterial::select('kd_pekerjaan', 'kd_kegiatan', 'nilai_dbkb_material')
                ->where([
                    'thn_dbkb_material' => $this->config()['year'],
                    'kd_kegiatan' => $setValue[$value['material']],
                    'kd_pekerjaan' => $value['kode']
                ])->first();
            $set = ['material' => $value['material'], 'nilai_dbkb_material' => $getMaterial->nilai_dbkb_material];
            array_push($result, $set);
        endforeach;
        return $result;
    }

    /**
     * hitung nilai bangunan standard
     */
    private function hitungNilaiStandard($setValue)
    {
        $kdBngLantai = (int)$setValue['kd_jpb'] . '_1_' . $setValue['tipe_bng'];
        $nilai = LspopDbkbStandard::selectRaw('min(nilai_dbkb_standard) as nilai')
            ->where('kd_jpb', $setValue['kd_jpb'])
            ->where('thn_dbkb_standard', $setValue['thn_dbkb_standard'])
            ->where('tipe_bng', '<>', $setValue['tipe_bng'])
            ->where('kd_bng_lantai', $kdBngLantai)
            ->first();
        return !is_null($nilai) ? $nilai->nilai : 0;
    }

    /**
     * hitung umur efektif
     */
    private function hitungUmurEfektif($setValue)
    {
        $thn            = $this->config()['year'];
        $thn_bangun     = $setValue['thn_dbkb_standard'];
        $thn_renov      = $setValue['thn_renovasi'];
        $kd_jpb         = $setValue['kd_jpb'];
        $umur_bangunan  = $thn - $thn_bangun;
        $renovasi       = $thn_renov - $thn_bangun;
        if ($kd_jpb == 1) {
            if (!empty($thn_renov)) {
                $umur_efektif = $thn - $thn_renov;
            } else {
                $umur_efektif = $thn - $thn_bangun;
            }
        } else {
            if ($thn_renov == '' && $umur_bangunan > 10) {
                $umur_efektif = (($thn - $thn_bangun) + (2 * 10)) / 3;
            } elseif ($thn_renov == '' && $umur_bangunan <= 10) {
                $umur_efektif = $thn - $thn_bangun;
            } elseif ($renovasi > 10) {
                $umur_efektif = (($thn - $thn_bangun) + (2 * 10)) / 3;
            } elseif ($renovasi <= 10) {
                $umur_efektif = (($thn - $thn_bangun) + (2 * ($thn - $thn_bangun))) / 3;
            } else {
                $umur_efektif = 0;
            }
        }

        return $umur_efektif;
    }

    /**
     * hitung penyusutan
     */
    private function hitungPenyusutan($setValue, $umurEfektif)
    {
        $getPenyusutan = LspopPenyusutan::select('nilai_penyusutan')
            ->where([
                'umur_efektif' => $umurEfektif,
                'kondisi_bng_susut' => $setValue['kondisi_bangunan'],
                'kd_range_penyusutan' => '1'
            ])
            ->first();
        $nilaiSusut = is_null($getPenyusutan) ? 0 : $getPenyusutan->nilai_penyusutan;
        $getFasilitasBangunan = DatFasilitasBangunan::where([
            'kd_kecamatan' => $setValue['kd_kecamatan'],
            'kd_kelurahan' => $setValue['kd_kelurahan'],
            'kd_blok' => $setValue['kd_blok'],
            'no_urut' => $setValue['no_urut'],
            'kd_jns_op' => $setValue['kd_jns_op'],
        ])->get();
        $jumlah = 0;
        foreach ($getFasilitasBangunan as $key => $row) :
            $kode_fas    = $row->KD_FASILITAS;
            $jlh_satuan  = $row->JML_SATUAN;
            $ob          = LspopFasNonDep::where(['kd_fasilitas' => $kode_fas])->first();
            $nilai_fas   = $ob->nilai_non_dep;
            $subnilai    =  $nilai_fas * $jlh_satuan;
            $jumlah += $subnilai;
        endforeach;

        return [
            'nilai_susut' => $nilaiSusut,
            'fas_susut' => $jumlah
        ];
    }

    /**
     * hitung sistem bangunan
     */
    private function hitungSistemBangunan($setValue, $hitungNilaiStandard, $hitungMaterial, $hitungPenyusutan)
    {
        /**
         * set value
         */
        $luasBng                = $setValue['tipe_bng'];
        $nilaiStandard          = $hitungNilaiStandard;
        $atap                   = $hitungMaterial[0]['material'] == 'atap' ? $hitungMaterial[0]['nilai_dbkb_material'] : 0;
        $dinding                = $hitungMaterial[0]['material'] == 'dinding' ? $hitungMaterial[0]['nilai_dbkb_material'] : 0;
        $lantai                 = $hitungMaterial[0]['material'] == 'lantai' ? $hitungMaterial[0]['nilai_dbkb_material'] : 0;
        $langit                 = $hitungMaterial[0]['material'] == 'langit_langit' ? $hitungMaterial[0]['nilai_dbkb_material'] : 0;
        $fasSusut               = $hitungPenyusutan['fas_susut'];
        $nilaiSusut             = $hitungPenyusutan['nilai_susut'];
        $fasilitasTidakSusust   =  0;

        /**
         * hitung
         */
        $utama               = $luasBng * $nilaiStandard;
        $material            = ($atap * $luasBng) + ($dinding * $luasBng) + ($lantai * $luasBng) + ($langit * $luasBng);
        $nilaiBelumSusut     = $utama + $material + $fasSusut;
        $penyusutan          = ($nilaiSusut * $nilaiBelumSusut) / 100; // utk nila susut hrs di konversi ke rupah dulu ya
        $nilaiKotor          = $nilaiBelumSusut - $penyusutan;
        $nilaiSistemBangunan = $nilaiKotor + $fasilitasTidakSusust;

        return $nilaiSistemBangunan;
    }

    function hitungLspop($setValue)
    {
        $hitungMaterial = $this->hitungMaterail($setValue);
        $hitungNilaiStandard = $this->hitungNilaiStandard($setValue);
        $hitungUmurEfektif = $this->hitungUmurEfektif($setValue);
        $hitungPenyusutan = $this->hitungPenyusutan($setValue, $hitungUmurEfektif);
        $hitungSistemBangunan = $this->hitungSistemBangunan($setValue, $hitungNilaiStandard, $hitungMaterial, $hitungPenyusutan);

        /**
         * hitung njop
         */
        $njopPerMeter = $hitungSistemBangunan / $setValue['tipe_bng'];
        $njopBangunan = $njopPerMeter * $setValue['tipe_bng'];
        return ['per_meter' => $njopPerMeter, 'njop' => $njopBangunan];
    }
}
