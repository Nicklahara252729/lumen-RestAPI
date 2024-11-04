<?php

namespace App\Repositories\Report\Pbb;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\Sppt\Sppt;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\DatObjekPajak\DatObjekPajak;
use App\Models\Refrensi\RefKecamatan\RefKecamatan;
use App\Models\Refrensi\RefKelurahan\RefKelurahan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\Report\Pbb\ReportPbbRepositories;

class EloquentReportPbbRepositories implements ReportPbbRepositories
{
    use Message, Response, Generator, Calculation;

    private $sppt;
    private $pembayaranSppt;
    private $datObjekPajak;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $refKecamatan;
    private $refKelurahan;

    public function __construct(
        Sppt $sppt,
        PembayaranSppt $pembayaranSppt,
        DatObjekPajak $datObjekPajak,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        RefKecamatan $refKecamatan,
        RefKelurahan $refKelurahan
    ) {
        /**
         * initialize model
         */
        $this->sppt = $sppt;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->refKecamatan = $refKecamatan;
        $this->refKelurahan = $refKelurahan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->provinsi  = globalAttribute()['kdProvinsi'];
        $this->kabupaten = globalAttribute()['kdKota'];
    }

    /**
     * rekap ketetapan
     */
    public function rekapKetetapan($tahun)
    {
        try {
            $data = $this->sppt->selectRaw('COUNT(NM_WP_SPPT) AS jumlah_sppt')
                ->selectRaw('SUM(LUAS_BUMI_SPPT) AS luas_bumi')
                ->selectRaw('SUM(NJOP_BUMI_SPPT) AS njop_bumi')
                ->selectRaw('SUM(LUAS_BNG_SPPT) AS luas_bangunan')
                ->selectRaw('SUM(NJOP_BNG_SPPT) AS njop_bangunan')
                ->selectRaw('SUM(PBB_YG_HARUS_DIBAYAR_SPPT) AS ketetapan')
                ->where('THN_PAJAK_SPPT', $tahun)
                ->first();
            $data['jumlah_op'] = $this->datObjekPajak->count();
            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data detail ketetapan
     */
    public function detailKetetapan($tahun)
    {
        try {

            /**
             * data sppt
             */
            $data = $this->sppt->select('KD_KECAMATAN', 'KD_KELURAHAN')
                ->selectRaw("COUNT(KD_KELURAHAN) AS total_sppt")
                ->selectRaw("SUM(LUAS_BUMI_SPPT) AS luas_bumi")
                ->selectRaw("SUM(NJOP_BUMI_SPPT) AS njop_bumi")
                ->selectRaw("SUM(LUAS_BNG_SPPT) AS luas_bangunan")
                ->selectRaw("SUM(NJOP_BNG_SPPT) AS njop_bangunan")
                ->selectRaw("SUM(PBB_YG_HARUS_DIBAYAR_SPPT) AS ketetapan_pbb")
                ->whereIn('KD_KECAMATAN', ['010', '020', '030', '040', '050'])
                ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                ->where('THN_PAJAK_SPPT', $tahun)
                ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                ->get();

            $kecamatanKelurahan = kecamatanKelurahan();

            /**
             * set new array for data sppt
             */
            $newData = [];
            foreach ($kecamatanKelurahan as $kecamatan => $kelurahanArr) {
                foreach ($kelurahanArr as $kelurahan) {
                    $total_sppt = 0;
                    $luas_bumi = 0;
                    $njop_bumi = 0;
                    $luas_bangunan = 0;
                    $njop_bangunan = 0;
                    $ketetapan_pbb = 0;

                    foreach ($data as $datas) {
                        if ($datas['KD_KECAMATAN'] === $kecamatan && $datas['KD_KELURAHAN'] === $kelurahan) {
                            $total_sppt += intval($datas['total_sppt']);
                            $luas_bumi += intval($datas['luas_bumi']);
                            $njop_bumi += intval($datas['njop_bumi']);
                            $luas_bangunan += intval($datas['luas_bangunan']);
                            $njop_bangunan += intval($datas['njop_bangunan']);
                            $ketetapan_pbb += intval($datas['ketetapan_pbb']);
                        }
                    }

                    $newData[] = [
                        'KD_KECAMATAN' => $kecamatan,
                        'KD_KELURAHAN' => $kelurahan,
                        'total_sppt' => $total_sppt,
                        'luas_bumi' => $luas_bumi,
                        'njop_bumi' => $njop_bumi,
                        'luas_bangunan' => $luas_bangunan,
                        'njop_bangunan' => $njop_bangunan,
                        'ketetapan_pbb' => $ketetapan_pbb,
                    ];
                }
            }

            /**
             * new data
             */
            $output = [];
            foreach ($newData as $key => $value) :

                /**
                 * nama kelurahan
                 */
                $getKelurahan = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $value['KD_KECAMATAN'], 'KD_KELURAHAN' => $value['KD_KELURAHAN']]);

                $totalSppt = $value['total_sppt'];
                $luasBumi = $value['luas_bumi'];
                $njopBumi = $value['njop_bumi'];
                $luasBangunan = $value['luas_bangunan'];
                $njopBangunan = $value['njop_bangunan'];
                $ketetapanPbb = $value['ketetapan_pbb'];

                /**
                 * set kelurahan data
                 */
                $setKelurahan = [
                    'kecamatan' => $value['KD_KECAMATAN'],
                    'nama' => $value['KD_KELURAHAN'] . ' - ' . $getKelurahan->NM_KELURAHAN,
                    'sppt' => $totalSppt,
                    'bumi' => [
                        'luas' => $luasBumi,
                        'njop' => $njopBumi
                    ],
                    'bangunan' => [
                        'luas' => $luasBangunan,
                        'njop' => $njopBangunan
                    ],
                    'ketetapan' => $ketetapanPbb,

                ];
                array_push($output, $setKelurahan);

            endforeach;

            // Variabel untuk menyimpan hasil akhir
            $groupedData = [];

            // Mengelompokkan data kecamatan dan kelurahan serta menghitung total kecamatan
            foreach ($output as $item) :
                // Mengambil nomor kecamatan dari kode kecamatan
                $kecamatanNumber = substr($item['kecamatan'], -3);
                $namaKecamatan = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kecamatanNumber])->NM_KECAMATAN;

                // Mengambil kode kelurahan dari nama kelurahan
                $kelurahanCode = substr($item['nama'], 0, 3);

                // Membuat indeks kecamatan jika belum ada
                if (!isset($groupedData[$kecamatanNumber])) {
                    $groupedData[$kecamatanNumber] = [
                        'kecamatan' => "12.76.{$kecamatanNumber} - {$namaKecamatan}",
                        'kelurahan' => [],
                        'jumlah' => [
                            'sppt' => 0,
                            'bumi' => [
                                'luas' => 0,
                                'njop' => 0
                            ],
                            'bangunan' => [
                                'luas' => 0,
                                'njop' => 0
                            ],
                            'ketetapan' => 0,
                        ]
                    ];
                }

                // Menambahkan data kelurahan ke dalam kelompok kecamatan
                $groupedData[$kecamatanNumber]['kelurahan'][] = [
                    'nama' => $item['nama'],
                    'sppt' => $item['sppt'],
                    'bumi' => $item['bumi'],
                    'bangunan' => $item['bangunan'],
                    'ketetapan' => $item['ketetapan']
                ];

                // Menghitung total kecamatan
                if (isset($groupedData[$kecamatanNumber]['jumlah'])) {
                    $groupedData[$kecamatanNumber]['jumlah']['sppt'] += $item['sppt'];

                    $groupedData[$kecamatanNumber]['jumlah']['bumi']['luas'] += $item['bumi']['luas'];
                    $groupedData[$kecamatanNumber]['jumlah']['bumi']['njop'] += $item['bumi']['njop'];

                    $groupedData[$kecamatanNumber]['jumlah']['bangunan']['luas'] += $item['bangunan']['luas'];
                    $groupedData[$kecamatanNumber]['jumlah']['bangunan']['njop'] += $item['bangunan']['njop'];

                    $groupedData[$kecamatanNumber]['jumlah']['ketetapan'] += $item['ketetapan'];
                }
            endforeach;

            // Mengubah array asosiatif ke dalam bentuk array untuk hasil akhir
            $finalResult = array_values($groupedData);
            $response  = $this->successData($this->outputMessage('data', count($finalResult)), $finalResult);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data rincian ketetapan
     */
    public function rincianKetetapan($request)
    {
        try {
            if (($request->kdKecamatan == 'all' || empty($request->kdKecamatan)) && ($request->kdKelurahan == 'all' || empty($request->kdKelurahan))) :
                $where = ['THN_PAJAK_SPPT' => $request->tahun];
            elseif ($request->kdKecamatan != 'all' && ($request->kdKelurahan == 'all' || empty($request->kdKelurahan))) :
                $where = [
                    'KD_KECAMATAN' => $request->kdKecamatan,
                    'THN_PAJAK_SPPT' => $request->tahun
                ];
            else :
                $where = [
                    'KD_KECAMATAN' => $request->kdKecamatan,
                    'KD_KELURAHAN' => $request->kdKelurahan,
                    'THN_PAJAK_SPPT' => $request->tahun
                ];
            endif;

            $dataSppt = $this->sppt->select(
                'KD_PROPINSI',
                'KD_DATI2',
                'KD_KECAMATAN',
                'KD_KELURAHAN',
                'KD_BLOK',
                'NO_URUT',
                'KD_JNS_OP',
                'NM_WP_SPPT',
                'PBB_YG_HARUS_DIBAYAR_SPPT',
                'LUAS_BUMI_SPPT',
                'NJOP_BUMI_SPPT',
                'LUAS_BNG_SPPT',
                'NJOP_BNG_SPPT',
            )
                ->where($where)
                ->get();

            $data = [];
            foreach ($dataSppt as $key => $value) :
                $getObjekPajak = $this->datObjekPajak->select('JALAN_OP')->where([
                    'KD_PROPINSI' => $value->KD_PROPINSI,
                    'KD_DATI2' => $value->KD_DATI2,
                    'KD_KECAMATAN' => $value->KD_KECAMATAN,
                    'KD_KELURAHAN' => $value->KD_KELURAHAN,
                    'KD_BLOK' => $value->KD_BLOK,
                    'NO_URUT' => $value->NO_URUT,
                    'KD_JNS_OP' => $value->KD_JNS_OP,
                ])->first();
                $set = [
                    'NOP' => $this->nop($value->KD_KECAMATAN, $value->KD_KELURAHAN, $value->KD_BLOK, $value->NO_URUT, $value->KD_JNS_OP),
                    'NM_WP_SPPT' => $value->NM_WP_SPPT,
                    'JALAN_OP' => $getObjekPajak->JALAN_OP,
                    'LUAS_BUMI_SPPT' => $value->LUAS_BUMI_SPPT,
                    'NJOP_BUMI_SPPT' => $value->NJOP_BUMI_SPPT,
                    'LUAS_BNG_SPPT' => $value->LUAS_BNG_SPPT,
                    'NJOP_BNG_SPPT' => $value->NJOP_BNG_SPPT,
                    'PBB_YG_HARUS_DIBAYAR_SPPT' => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                ];
                array_push($data, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data rincian realisasi
     */
    public function rincianRealisasi($request)
    {
        try {
            $data = $this->pembayaranSppt->select('THN_PAJAK_SPPT AS tahun')
                ->selectRaw("DATE_FORMAT(TGL_PEMBAYARAN_SPPT, '%d %M %Y') AS tgl_bayar")
                ->selectRaw('COALESCE(DENDA_SPPT, 0) AS denda')
                ->selectRaw('COALESCE(JML_SPPT_YG_DIBAYAR, 0) AS jumlah_bayar')
                ->selectRaw("CONCAT_WS('.',KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) AS nop")
                ->whereBetween('TGL_PEMBAYARAN_SPPT', [$request->startDate, $request->endDate]);

            if ($request->kdKecamatan != 'all' && ($request->kdKelurahan == 'all' || empty($request->kdKelurahan))) :
                $data = $data->where(['KD_KECAMATAN' => $request->kdKecamatan]);
            elseif ($request->kdKecamatan != 'all' && $request->kdKelurahan != 'all') :
                $data = $data->where([
                    'KD_KECAMATAN' => $request->kdKecamatan,
                    'KD_KELURAHAN' => $request->kdKelurahan
                ]);
            endif;
            $data = $data->orderBy('TGL_PEMBAYARAN_SPPT', 'asc')->get();

            $output = [];
            foreach ($data as $key => $value) :
                // $getSppt = $this->sppt->select('PBB_YG_HARUS_DIBAYAR_SPPT AS pokok', 'NM_WP_SPPT AS nama_wp')
                //     ->whereRaw("CONCAT(KD_PROPINSI,KD_DATI2,KD_KECAMATAN,KD_KELURAHAN,KD_BLOK,NO_URUT,KD_JNS_OP) = REPLACE ('" . $value->nop . "','.','') AND THN_PAJAK_SPPT = '" . $value->tahun . "'")
                //     ->limit(1)
                //     ->first();
                $set = [
                    'nop' => $value->nop,
                    'nama_wp' => '-',
                    'tahun' => $value->tahun,
                    'pokok' => $value->jumlah_bayar - $value->denda,
                    'denda' => $value->denda,
                    'jumlah_bayar' => $value->jumlah_bayar,
                    'tgl_bayar' => $value->tgl_bayar,
                ];
                array_push($output, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($output)), $output);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data rekap realisasi
     */
    public function rekapRealisasi($startDate, $endDate)
    {
        try {

            /**
             * mencari selisih hari
             */
            $startDateParse = Carbon::parse($startDate);
            $endDateParse = Carbon::parse($endDate);
            $startDateCheck = $startDateParse->format('d') . '-' . $startDateParse->format('m');
            $startYear = $startDateParse->format('Y');
            $endYear = $endDateParse->format('Y');
            //$diffInDays = $endDate->diffInDays($startDateParse);
            $newStartDate = $startYear . '-01-01';
            $newEndDate = $startDateParse->subDays(1);
            if ($startYear != $endYear) :
                throw new \Exception($this->outputMessage('unmatch', 'tahun'));
            endif;

            /**
             * data minggu ini
             */
            $dataMingguIni = $this->pembayaranSppt->select('KD_KECAMATAN', 'KD_KELURAHAN')
                ->selectRaw("SUM(DENDA_SPPT) AS total_denda")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) AS total_bayar")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) - SUM(DENDA_SPPT) AS pokok")
                ->selectRaw("COUNT(KD_KELURAHAN) AS total_stts")
                ->whereIn('KD_KECAMATAN', ['010', '020', '030', '040', '050'])
                ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                ->whereRaw('DATE(TGL_PEMBAYARAN_SPPT) BETWEEN "' . $startDate . '" AND "' . $endDate . '"')
                ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                ->get();

            /**
             * data minggu lalu
             */
            $dataMingguLalu = $this->pembayaranSppt->select('KD_KECAMATAN', 'KD_KELURAHAN')
                ->selectRaw("SUM(DENDA_SPPT) AS total_denda")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) AS total_bayar")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) - SUM(DENDA_SPPT) AS pokok")
                ->selectRaw("COUNT(KD_KELURAHAN) AS total_stts")
                ->whereIn('KD_KECAMATAN', ['010', '020', '030', '040', '050'])
                ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                ->whereRaw('DATE(TGL_PEMBAYARAN_SPPT) BETWEEN "' . $newStartDate . '" AND "' . $newEndDate . '"')
                ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                ->get();

            $kecamatanKelurahan = [
                '010' => ['001', '002', '003', '004', '005', '006', '007', '008'],
                '020' => ['001', '002', '003', '004', '005', '006', '007'],
                '030' => ['001', '002', '003', '004', '005', '006', '007'],
                '040' => ['001', '002', '003', '004', '005', '006', '007', '008', '009'],
                '050' => ['001', '002', '003', '004', '005', '006'],
            ];

            /**
             * set new array for data minggu ini
             */
            $newDataMingguIni = [];
            foreach ($kecamatanKelurahan as $kecamatan => $kelurahanArr) {
                foreach ($kelurahanArr as $kelurahan) {
                    $total_stts = 0;
                    $total_denda = 0;
                    $total_bayar = 0;
                    $pokok = 0;

                    foreach ($dataMingguIni as $data) {
                        if ($data['KD_KECAMATAN'] === $kecamatan && $data['KD_KELURAHAN'] === $kelurahan) {
                            $total_stts += intval($data['total_stts']);
                            $total_denda += intval($data['total_denda']);
                            $total_bayar += intval($data['total_bayar']);
                            $pokok += intval($data['pokok']);
                        }
                    }

                    $newDataMingguIni[] = [
                        'KD_KECAMATAN' => $kecamatan,
                        'KD_KELURAHAN' => $kelurahan,
                        'total_stts' => $total_stts,
                        'total_denda' => $total_denda,
                        'total_bayar' => $total_bayar,
                        'pokok' => $pokok,
                    ];
                }
            }

            /**
             * set to array for data minggu lalu
             */
            $newDataMingguLalu = [];
            foreach ($kecamatanKelurahan as $kecamatan => $kelurahanArr) {
                foreach ($kelurahanArr as $kelurahan) {
                    $total_stts = 0;
                    $total_denda = 0;
                    $total_bayar = 0;
                    $pokok = 0;

                    foreach ($dataMingguLalu as $data) {
                        if ($data['KD_KECAMATAN'] === $kecamatan && $data['KD_KELURAHAN'] === $kelurahan) {
                            $total_stts += intval($data['total_stts']);
                            $total_denda += intval($data['total_denda']);
                            $total_bayar += intval($data['total_bayar']);
                            $pokok += intval($data['pokok']);
                        }
                    }

                    $newDataMingguLalu[] = [
                        'KD_KECAMATAN' => $kecamatan,
                        'KD_KELURAHAN' => $kelurahan,
                        'total_stts' => $total_stts,
                        'total_denda' => $total_denda,
                        'total_bayar' => $total_bayar,
                        'pokok' => $pokok,
                    ];
                }
            }

            /**
             * new data
             */
            $output = [];
            foreach ($newDataMingguIni as $key => $value) :

                /**
                 * nama kelurahan
                 */
                $getKelurahan = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $value['KD_KECAMATAN'], 'KD_KELURAHAN' => $value['KD_KELURAHAN']]);

                $sttsMingguLaluSum = $startDateCheck == '01-01' ? 0 : $newDataMingguLalu[$key]['total_stts'];
                $pokokMingguLaluSum = $startDateCheck == '01-01' ? 0 : $newDataMingguLalu[$key]['pokok'];
                $dendaMingguLaluSum = $startDateCheck == '01-01' ? 0 : $newDataMingguLalu[$key]['total_denda'];
                $totalMingguLaluSum = $startDateCheck == '01-01' ? 0 : $newDataMingguLalu[$key]['total_bayar'];

                $sttsMingguIniSum = $value['total_stts'];
                $pokokMingguIniSum = $value['pokok'];
                $dendaMingguIniSum = $value['total_denda'];
                $totalMingguIniSum = $value['total_bayar'];

                $sttsSdMingguIniSum = $sttsMingguLaluSum + $sttsMingguIniSum;
                $pokokSdMingguIniSum = $pokokMingguLaluSum + $pokokMingguIniSum;
                $dendaSdMingguIniSum = $dendaMingguLaluSum + $dendaMingguIniSum;
                $totalSdMingguIniSum = $totalMingguLaluSum + $totalMingguIniSum;

                /**
                 * set kelurahan data
                 */
                $setKelurahan = [
                    'kecamatan' => $value['KD_KECAMATAN'],
                    'nama' => $value['KD_KELURAHAN'] . ' - ' . $getKelurahan->NM_KELURAHAN,
                    'sd_minggu_lalu' => [
                        'stts' => (int) $sttsMingguLaluSum,
                        'pokok' => (int) $pokokMingguLaluSum,
                        'denda' => (int) $dendaMingguLaluSum,
                        'total' => (int) $totalMingguLaluSum
                    ],
                    'minggu_ini' => [
                        'stts' => (int) $value['total_stts'],
                        'pokok' => (int) $value['pokok'],
                        'denda' => (int) $value['total_denda'],
                        'total' => (int) $value['total_bayar']
                    ],
                    'sd_minggu_ini' => [
                        'stts' => (int) $sttsSdMingguIniSum,
                        'pokok' => $pokokSdMingguIniSum,
                        'denda' => (int) $dendaSdMingguIniSum,
                        'total' => (int) $totalSdMingguIniSum
                    ],

                ];
                array_push($output, $setKelurahan);

            endforeach;
            // Variabel untuk menyimpan hasil akhir
            $groupedData = [];

            // Mengelompokkan data kecamatan dan kelurahan serta menghitung total kecamatan
            foreach ($output as $item) :
                // Mengambil nomor kecamatan dari kode kecamatan
                $kecamatanNumber = substr($item['kecamatan'], -3);
                $namaKecamatan = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kecamatanNumber])->NM_KECAMATAN;

                // Mengambil kode kelurahan dari nama kelurahan
                $kelurahanCode = substr($item['nama'], 0, 3);

                // Membuat indeks kecamatan jika belum ada
                if (!isset($groupedData[$kecamatanNumber])) {
                    $groupedData[$kecamatanNumber] = [
                        'kecamatan' => "12.76.{$kecamatanNumber} - {$namaKecamatan}",
                        'kelurahan' => [],
                        'jumlah' => [
                            'sd_minggu_lalu' => [
                                'stts' => 0,
                                'pokok' => 0,
                                'denda' => 0,
                                'total' => 0
                            ],
                            'minggu_ini' => [
                                'stts' => 0,
                                'pokok' => 0,
                                'denda' => 0,
                                'total' => 0
                            ],
                            'sd_minggu_ini' => [
                                'stts' => 0,
                                'pokok' => 0,
                                'denda' => 0,
                                'total' => 0
                            ]
                        ]
                    ];
                }

                // Menambahkan data kelurahan ke dalam kelompok kecamatan
                $groupedData[$kecamatanNumber]['kelurahan'][] = [
                    'nama' => $item['nama'],
                    'sd_minggu_lalu' => $item['sd_minggu_lalu'],
                    'minggu_ini' => $item['minggu_ini'],
                    'sd_minggu_ini' => $item['sd_minggu_ini']
                ];

                // Menghitung total kecamatan
                if (isset($groupedData[$kecamatanNumber]['jumlah'])) {
                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_lalu']['stts'] += $item['sd_minggu_lalu']['stts'];
                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_lalu']['pokok'] += $item['sd_minggu_lalu']['pokok'];
                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_lalu']['denda'] += $item['sd_minggu_lalu']['denda'];
                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_lalu']['total'] += $item['sd_minggu_lalu']['total'];

                    $groupedData[$kecamatanNumber]['jumlah']['minggu_ini']['stts'] += $item['minggu_ini']['stts'];
                    $groupedData[$kecamatanNumber]['jumlah']['minggu_ini']['pokok'] += $item['minggu_ini']['pokok'];
                    $groupedData[$kecamatanNumber]['jumlah']['minggu_ini']['denda'] += $item['minggu_ini']['denda'];
                    $groupedData[$kecamatanNumber]['jumlah']['minggu_ini']['total'] += $item['minggu_ini']['total'];

                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_ini']['stts'] += $item['sd_minggu_ini']['stts'];
                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_ini']['pokok'] += $item['sd_minggu_ini']['pokok'];
                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_ini']['denda'] += $item['sd_minggu_ini']['denda'];
                    $groupedData[$kecamatanNumber]['jumlah']['sd_minggu_ini']['total'] += $item['sd_minggu_ini']['total'];
                }
            endforeach;

            // Mengubah array asosiatif ke dalam bentuk array untuk hasil akhir
            $finalResult = array_values($groupedData);

            $response  = $this->successData($this->outputMessage('data', count($output)), $finalResult);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data detail realisasi
     */
    public function detailRealisasi($startDate, $endDate)
    {
        try {

            /**
             * mencari selisih hari
             */
            $startDateParse = Carbon::parse($startDate);
            $endDateParse = Carbon::parse($endDate);
            $startDateCheck = $startDateParse->format('d') . '-' . $startDateParse->format('m');
            $startYear = $startDateParse->format('Y');
            $endYear = $endDateParse->format('Y');
            $newStartDate = '1995-01-01';
            $newEndDate = ($startYear - 1) . '-12-31';
            if ($startYear != $endYear) :
                throw new \Exception($this->outputMessage('unmatch', 'tahun'));
            endif;

            /**
             * data periode ini
             */
            $dataPeriodeIni = $this->pembayaranSppt->select('KD_KECAMATAN', 'KD_KELURAHAN')
                ->selectRaw("SUM(DENDA_SPPT) AS total_denda")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) AS total_bayar")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) - SUM(DENDA_SPPT) AS pokok")
                ->selectRaw("COUNT(KD_KELURAHAN) AS total_stts")
                ->whereIn('KD_KECAMATAN', ['010', '020', '030', '040', '050'])
                ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                ->where('THN_PAJAK_SPPT', $startYear)
                ->whereRaw('DATE(TGL_PEMBAYARAN_SPPT) BETWEEN "' . $startDate . '" AND "' . $endDate . '"')
                ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                ->get();

            /**
             * data periode lalu
             */
            $datPeriodeLalu = $this->pembayaranSppt->select('KD_KECAMATAN', 'KD_KELURAHAN')
                ->selectRaw("SUM(DENDA_SPPT) AS total_denda")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) AS total_bayar")
                ->selectRaw("SUM(JML_SPPT_YG_DIBAYAR) - SUM(DENDA_SPPT) AS pokok")
                ->selectRaw("COUNT(KD_KELURAHAN) AS total_stts")
                ->whereIn('KD_KECAMATAN', ['010', '020', '030', '040', '050'])
                ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                ->whereRaw('DATE(TGL_PEMBAYARAN_SPPT) BETWEEN "' . $newStartDate . '" AND "' . $newEndDate . '"')
                ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                ->get();

            /**
             * data ketetapan
             */
            $dataKetetapan = $this->sppt->select('KD_KECAMATAN', 'KD_KELURAHAN')
                ->selectRaw("COUNT(KD_KELURAHAN) AS stts")
                ->selectRaw("SUM(PBB_YG_HARUS_DIBAYAR_SPPT) AS ketetapan")
                ->whereIn('KD_KECAMATAN', ['010', '020', '030', '040', '050'])
                ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                ->where('THN_PAJAK_SPPT', $startYear)
                ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                ->get();

            $kecamatanKelurahan = $kecamatanKelurahan = kecamatanKelurahan();

            /**
             * set new array for data periode ini
             */
            $newPeriodeIni = [];
            foreach ($kecamatanKelurahan as $kecamatan => $kelurahanArr) {
                foreach ($kelurahanArr as $kelurahan) {
                    $total_stts = 0;
                    $total_denda = 0;
                    $total_bayar = 0;
                    $pokok = 0;

                    foreach ($dataPeriodeIni as $data) {
                        if ($data['KD_KECAMATAN'] === $kecamatan && $data['KD_KELURAHAN'] === $kelurahan) {
                            $total_stts += intval($data['total_stts']);
                            $total_denda += intval($data['total_denda']);
                            $total_bayar += intval($data['total_bayar']);
                            $pokok += intval($data['pokok']);
                        }
                    }

                    $newPeriodeIni[] = [
                        'KD_KECAMATAN' => $kecamatan,
                        'KD_KELURAHAN' => $kelurahan,
                        'total_stts' => $total_stts,
                        'total_denda' => $total_denda,
                        'total_bayar' => $total_bayar,
                        'pokok' => $pokok,
                    ];
                }
            }

            /**
             * set to array for data periode lalu
             */
            $newDataPeriodeLalu = [];
            foreach ($kecamatanKelurahan as $kecamatan => $kelurahanArr) {
                foreach ($kelurahanArr as $kelurahan) {
                    $total_stts = 0;
                    $total_denda = 0;
                    $total_bayar = 0;
                    $pokok = 0;

                    foreach ($datPeriodeLalu as $data) {
                        if ($data['KD_KECAMATAN'] === $kecamatan && $data['KD_KELURAHAN'] === $kelurahan) {
                            $total_stts += intval($data['total_stts']);
                            $total_denda += intval($data['total_denda']);
                            $total_bayar += intval($data['total_bayar']);
                            $pokok += intval($data['pokok']);
                        }
                    }

                    $newDataPeriodeLalu[] = [
                        'KD_KECAMATAN' => $kecamatan,
                        'KD_KELURAHAN' => $kelurahan,
                        'total_stts' => $total_stts,
                        'total_denda' => $total_denda,
                        'total_bayar' => $total_bayar,
                        'pokok' => $pokok,
                    ];
                }
            }

            /**
             * set to array for data ketetapan lalu
             */
            $newDataKetetapan = [];
            foreach ($kecamatanKelurahan as $kecamatan => $kelurahanArr) {
                foreach ($kelurahanArr as $kelurahan) {
                    $stts = 0;
                    $ketetapan = 0;

                    foreach ($dataKetetapan as $data) {
                        if ($data['KD_KECAMATAN'] === $kecamatan && $data['KD_KELURAHAN'] === $kelurahan) {
                            $stts += intval($data['stts']);
                            $ketetapan += intval($data['ketetapan']);
                        }
                    }

                    $newDataKetetapan[] = [
                        'KD_KECAMATAN' => $kecamatan,
                        'KD_KELURAHAN' => $kelurahan,
                        'stts' => $stts,
                        'ketetapan' => $ketetapan,
                    ];
                }
            }

            /**
             * new data
             */
            $output = [];
            foreach ($newPeriodeIni as $key => $value) :

                /**
                 * nama kelurahan
                 */
                $getKelurahan = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $value['KD_KECAMATAN'], 'KD_KELURAHAN' => $value['KD_KELURAHAN']]);

                $sttsPeriodeLaluSum = $newDataPeriodeLalu[$key]['total_stts'];
                $pokokPeriodeLaluSum = $newDataPeriodeLalu[$key]['pokok'];
                $dendaPeriodeLaluSum = $newDataPeriodeLalu[$key]['total_denda'];
                $totalPeriodeLaluSum = $newDataPeriodeLalu[$key]['total_bayar'];

                $sttsKetetapanSum = $newDataKetetapan[$key]['stts'];
                $ketetapanSum = $newDataKetetapan[$key]['ketetapan'];

                $sttsPeriodeIniSum = $value['total_stts'];
                $pokokPeriodeIniSum = $value['pokok'];
                $dendaPeriodeIniSum = $value['total_denda'];
                $totalPeriodeIniSum = $value['total_bayar'];

                $sttsSdPeriodeIniSum = $sttsPeriodeLaluSum + $sttsPeriodeIniSum;
                $pokokSdPeriodeIniSum = $pokokPeriodeLaluSum + $pokokPeriodeIniSum;
                $dendaSdPeriodeIniSum = $dendaPeriodeLaluSum + $dendaPeriodeIniSum;
                $totalSdPeriodeIniSum = $totalPeriodeLaluSum + $totalPeriodeIniSum;

                /**
                 * set kelurahan data
                 */
                $persenPeriodeIni = $value['total_bayar'] == 0 || $ketetapanSum == 0 ? 0 : number_format(($value['total_bayar'] / $ketetapanSum) * 100, 2);
                $persenTotalRealisasi = $value['total_bayar'] == 0 || $ketetapanSum == 0 ? 0 : round((($value['total_bayar'] + $totalSdPeriodeIniSum) / $ketetapanSum) * 100, 2);
                $setKelurahan = [
                    'kecamatan' => $value['KD_KECAMATAN'],
                    'nama' => $value['KD_KELURAHAN'] . ' - ' . $getKelurahan->NM_KELURAHAN,
                    'ketetapan' => [
                        'stts' => (int) $sttsKetetapanSum,
                        'ketetapan' => (int) $ketetapanSum
                    ],
                    'realisasi_periode_ini' => [
                        'stts' => (int) $value['total_stts'],
                        'pokok' => (int) $value['pokok'],
                        'denda' => (int) $value['total_denda'],
                        'total' => (int) $value['total_bayar'],
                        'persen' => (float) $persenPeriodeIni
                    ],
                    'realisasi_periode_lalu' => [
                        'stts' => (int) $sttsPeriodeLaluSum,
                        'pokok' => (int) $pokokPeriodeLaluSum,
                        'denda' => (int) $dendaPeriodeLaluSum,
                        'total' => (int) $totalPeriodeLaluSum,
                    ],
                    'total_realisasi' => [
                        'stts' => (int) $value['total_stts'] + $sttsSdPeriodeIniSum,
                        'pokok' => $value['pokok'] + $pokokSdPeriodeIniSum,
                        'denda' => (int) $value['total_denda'] + $dendaSdPeriodeIniSum,
                        'total' => (int) $value['total_bayar'] + $totalSdPeriodeIniSum,
                        'persen' => (float) $persenTotalRealisasi
                    ],
                ];
                array_push($output, $setKelurahan);

            endforeach;

            // Variabel untuk menyimpan hasil akhir
            $groupedData = [];

            // Mengelompokkan data kecamatan dan kelurahan serta menghitung total kecamatan
            foreach ($output as $item) :
                // Mengambil nomor kecamatan dari kode kecamatan
                $kecamatanNumber = substr($item['kecamatan'], -3);
                $namaKecamatan = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kecamatanNumber])->NM_KECAMATAN;

                // Mengambil kode kelurahan dari nama kelurahan
                $kelurahanCode = substr($item['nama'], 0, 3);

                // Membuat indeks kecamatan jika belum ada
                if (!isset($groupedData[$kecamatanNumber])) {
                    $groupedData[$kecamatanNumber] = [
                        'kecamatan' => "12.76.{$kecamatanNumber} - {$namaKecamatan}",
                        'kelurahan' => [],
                        'jumlah' => [
                            'ketetapan' => [
                                'stts' => 0,
                                'ketetapan' => 0
                            ],
                            'realisasi_periode_ini' => [
                                'stts' => 0,
                                'pokok' => 0,
                                'denda' => 0,
                                'total' => 0,
                                'persen' => 0
                            ],
                            'realisasi_periode_lalu' => [
                                'stts' => 0,
                                'pokok' => 0,
                                'denda' => 0,
                                'total' => 0
                            ],
                            'total_realisasi' => [
                                'stts' => 0,
                                'pokok' => 0,
                                'denda' => 0,
                                'total' => 0,
                                'persen' => 0
                            ]
                        ]
                    ];
                }

                // Menambahkan data kelurahan ke dalam kelompok kecamatan
                $groupedData[$kecamatanNumber]['kelurahan'][] = [
                    'nama' => $item['nama'],
                    'ketetapan' => $item['ketetapan'],
                    'realisasi_periode_ini' => $item['realisasi_periode_ini'],
                    'realisasi_periode_lalu' => $item['realisasi_periode_lalu'],
                    'total_realisasi' => $item['total_realisasi']
                ];

                // Menghitung total kecamatan
                if (isset($groupedData[$kecamatanNumber]['jumlah'])) {
                    $groupedData[$kecamatanNumber]['jumlah']['ketetapan']['stts'] += $item['ketetapan']['stts'];
                    $groupedData[$kecamatanNumber]['jumlah']['ketetapan']['ketetapan'] += $item['ketetapan']['ketetapan'];

                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_ini']['stts'] += $item['realisasi_periode_ini']['stts'];
                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_ini']['pokok'] += $item['realisasi_periode_ini']['pokok'];
                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_ini']['denda'] += $item['realisasi_periode_ini']['denda'];
                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_ini']['total'] += $item['realisasi_periode_ini']['total'];
                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_ini']['persen'] += $item['realisasi_periode_ini']['persen'];

                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_lalu']['stts'] += $item['realisasi_periode_lalu']['stts'];
                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_lalu']['pokok'] += $item['realisasi_periode_lalu']['pokok'];
                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_lalu']['denda'] += $item['realisasi_periode_lalu']['denda'];
                    $groupedData[$kecamatanNumber]['jumlah']['realisasi_periode_lalu']['total'] += $item['realisasi_periode_lalu']['total'];

                    $groupedData[$kecamatanNumber]['jumlah']['total_realisasi']['stts'] += $item['total_realisasi']['stts'];
                    $groupedData[$kecamatanNumber]['jumlah']['total_realisasi']['pokok'] += $item['total_realisasi']['pokok'];
                    $groupedData[$kecamatanNumber]['jumlah']['total_realisasi']['denda'] += $item['total_realisasi']['denda'];
                    $groupedData[$kecamatanNumber]['jumlah']['total_realisasi']['total'] += $item['total_realisasi']['total'];
                    $groupedData[$kecamatanNumber]['jumlah']['total_realisasi']['persen'] += $item['total_realisasi']['persen'];
                }
            endforeach;

            // Mengubah array asosiatif ke dalam bentuk array untuk hasil akhir
            $finalResult = array_values($groupedData);

            $response  = $this->successData($this->outputMessage('data', count($finalResult)), $finalResult);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data rincian piutang
     */
    public function rincianPiutang($request)
    {
        try {
            $where = [];
            if ($request->kdKecamatan != 'all' && $request->kdKelurahan != 'all') :
                $where = [
                    'KD_KECAMATAN' => $request->kdKecamatan,
                    'KD_KELURAHAN' => $request->kdKelurahan,
                ];
            elseif ($request->kdKecamatan != 'all' && $request->kdKelurahan == 'all') :
                $where = [
                    'KD_KECAMATAN' => $request->kdKecamatan,
                ];
            endif;

            /**
             * get data sppt
             */
            $dataSppt = $this->sppt
                ->select(
                    'KD_PROPINSI',
                    'KD_DATI2',
                    'KD_KECAMATAN',
                    'KD_KELURAHAN',
                    'KD_BLOK',
                    'NO_URUT',
                    'KD_JNS_OP',
                    'THN_PAJAK_SPPT',
                    'NM_WP_SPPT',
                    'PBB_YG_HARUS_DIBAYAR_SPPT',
                    'TGL_JATUH_TEMPO_SPPT',
                    'LUAS_BUMI_SPPT',
                    'LUAS_BNG_SPPT'
                )
                ->where($where)
                ->where(['STATUS_PEMBAYARAN_SPPT' => 0])
                ->whereRaw('THN_PAJAK_SPPT BETWEEN "1994" AND "' . $request->tahun . '"')
                ->orderBy('THN_PAJAK_SPPT', 'asc')
                ->orderBy('KD_KECAMATAN', 'asc')
                ->orderBy('KD_KELURAHAN', 'asc')
                ->orderBy('KD_BLOK', 'asc')
                ->orderBy('NO_URUT', 'asc')
                ->get();

            /**
             * get objek pajak
             */
            $getObjekPajak = $this->datObjekPajak->select(
                'KD_BLOK',
                'NO_URUT',
                'KD_JNS_OP',
                'JALAN_OP'
            )
                ->where($where)
                ->get()
                ->toArray();

            $data = [];
            foreach ($dataSppt as $key => $value) :

                $kdBlok = $value->KD_BLOK;
                $noUrut = $value->NO_URUT;
                $kdJnsOp = $value->KD_JNS_OP;

                $filter = array_filter($getObjekPajak, function ($item) use ($kdBlok, $noUrut, $kdJnsOp) {
                    return $item["KD_BLOK"] === $kdBlok && $item["NO_URUT"] === $noUrut && $item["KD_JNS_OP"] === $kdJnsOp;
                });

                $jalanOp = '-';
                foreach ($filter as $item) :
                    $jalanOp = $item['JALAN_OP'];
                endforeach;


                $jumlahBulanDenda = $this->perhitunganBulanDendaPBB($value->TGL_JATUH_TEMPO_SPPT);
                $denda = $this->dendaPbb($jumlahBulanDenda, $value->PBB_YG_HARUS_DIBAYAR_SPPT, $value->THN_PAJAK_SPPT);

                $set = [
                    'NOP' => $this->nop($value->KD_KECAMATAN, $value->KD_KELURAHAN, $value->KD_BLOK, $value->NO_URUT, $value->KD_JNS_OP),
                    'NM_WP_SPPT' => $value->NM_WP_SPPT,
                    'JALAN_OP' => $jalanOp,
                    'THN_PAJAK_SPPT' => $value->THN_PAJAK_SPPT,
                    'PBB_YG_HARUS_DIBAYAR_SPPT' => $value->PBB_YG_HARUS_DIBAYAR_SPPT,
                    'DENDA' => $denda,
                    'JUMLAH_BAYAR' => $value->PBB_YG_HARUS_DIBAYAR_SPPT + $denda
                ];
                array_push($data, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data rekap piutang
     */
    public function rekapPiutang($request)
    {
        try {
            /**
             * data sppt
             */
            $tahun = $request->tahun;
            $data = $this->sppt->select('KD_KECAMATAN', 'KD_KELURAHAN')
                ->selectRaw("SUM(PBB_YG_HARUS_DIBAYAR_SPPT) AS pokok")
                ->whereIn('KD_KECAMATAN', ['010', '020', '030', '040', '050'])
                ->whereIn('KD_KELURAHAN', ['001', '002', '003', '004', '005', '006', '007', '008', '009'])
                ->whereRaw('THN_PAJAK_SPPT BETWEEN "1994" AND "' . $tahun . '"')
                ->groupBy('KD_KECAMATAN', 'KD_KELURAHAN')
                ->get();

            $kecamatanKelurahan = kecamatanKelurahan();

            /**
             * set new array for data sppt
             */
            $newData = [];
            foreach ($kecamatanKelurahan as $kecamatan => $kelurahanArr) {
                foreach ($kelurahanArr as $kelurahan) {
                    $pokok = 0;

                    foreach ($data as $datas) {
                        if ($datas['KD_KECAMATAN'] === $kecamatan && $datas['KD_KELURAHAN'] === $kelurahan) {
                            $pokok += intval($datas['pokok']);
                        }
                    }

                    $newData[] = [
                        'KD_KECAMATAN' => $kecamatan,
                        'KD_KELURAHAN' => $kelurahan,
                        'pokok' => $pokok,
                    ];
                }
            }

            /**
             * new data
             */
            $output = [];
            foreach ($newData as $key => $value) :

                /**
                 * nama kelurahan
                 */
                $getKelurahan = $this->checkerHelpers->refrensiKelurahanChecker(['KD_KECAMATAN' => $value['KD_KECAMATAN'], 'KD_KELURAHAN' => $value['KD_KELURAHAN']]);

                $totalDenda = 0;
                $getSppt = $this->sppt->select('TGL_JATUH_TEMPO_SPPT', 'PBB_YG_HARUS_DIBAYAR_SPPT')
                    ->where(['KD_KECAMATAN' => $value['KD_KECAMATAN'], 'KD_KELURAHAN' => $value['KD_KELURAHAN']])
                    ->get();
                foreach ($getSppt as $key => $valueSppt) :
                    $jumlahBulanDenda = $this->perhitunganBulanDendaPBB($valueSppt->TGL_JATUH_TEMPO_SPPT);
                    $denda = $this->dendaPbb($jumlahBulanDenda, $valueSppt->PBB_YG_HARUS_DIBAYAR_SPPT, $tahun);
                    $totalDenda += $denda;
                endforeach;

                $pokok = $value['pokok'];

                /**
                 * set kelurahan data
                 */
                $setKelurahan = [
                    'kecamatan' => $value['KD_KECAMATAN'],
                    'nama' => $value['KD_KELURAHAN'] . ' - ' . $getKelurahan->NM_KELURAHAN,
                    'pokok' => $pokok,
                    'denda' => $totalDenda,
                    'jumlah' => $pokok + $totalDenda,

                ];
                array_push($output, $setKelurahan);
            endforeach;

            // Variabel untuk menyimpan hasil akhir
            $groupedData = [];

            // Mengelompokkan data kecamatan dan kelurahan serta menghitung total kecamatan
            foreach ($output as $item) :
                // Mengambil nomor kecamatan dari kode kecamatan
                $kecamatanNumber = substr($item['kecamatan'], -3);
                $namaKecamatan = $this->checkerHelpers->refrensiKecamatanChecker(['KD_KECAMATAN' => $kecamatanNumber])->NM_KECAMATAN;

                // Mengambil kode kelurahan dari nama kelurahan
                $kelurahanCode = substr($item['nama'], 0, 3);

                // Membuat indeks kecamatan jika belum ada
                if (!isset($groupedData[$kecamatanNumber])) {
                    $groupedData[$kecamatanNumber] = [
                        'kecamatan' => "12.76.{$kecamatanNumber} - {$namaKecamatan}",
                        'kelurahan' => [],
                        'jumlah' => [
                            'pokok' => 0,
                            'denda' => 0,
                            'jumlah' => 0,
                        ]
                    ];
                }

                // Menambahkan data kelurahan ke dalam kelompok kecamatan
                $groupedData[$kecamatanNumber]['kelurahan'][] = [
                    'nama' => $item['nama'],
                    'pokok' => $item['pokok'],
                    'denda' => $item['denda'],
                    'jumlah' => $item['jumlah'],
                ];

                // Menghitung total kecamatan
                if (isset($groupedData[$kecamatanNumber]['jumlah'])) {
                    $groupedData[$kecamatanNumber]['jumlah']['pokok'] += $item['pokok'];
                    $groupedData[$kecamatanNumber]['jumlah']['denda'] += $item['denda'];
                    $groupedData[$kecamatanNumber]['jumlah']['jumlah'] += $item['jumlah'];
                }
            endforeach;

            // Mengubah array asosiatif ke dalam bentuk array untuk hasil akhir
            $finalResult = array_values($groupedData);
            $response  = $this->successData($this->outputMessage('data', count($finalResult)), $finalResult);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
