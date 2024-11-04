<?php

namespace App\Repositories\Public;

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

/**
 * import models
 */

use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\Sppt\Sppt;
use App\Models\Layanan\Layanan\Layanan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Public\PublicRepositories;

class EloquentPublicRepositories implements PublicRepositories
{
    use Message, Response;

    private $checkerHelpers;
    private $pembayaranSppt;
    private $sppt;
    private $layanan;
    private $year;
    private $secondDb;
    private $thirdDb;
    private $month;
    private $startDate;
    private $endDate;

    public function __construct(
        PembayaranSppt $pembayaranSppt,
        Sppt $sppt,
        Layanan $layanan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->pembayaranSppt = $pembayaranSppt;
        $this->sppt = $sppt;
        $this->layanan = $layanan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->year = Carbon::now()->format('Y');
        $this->month = Carbon::now()->format('n');
        $this->startDate = Carbon::now();
        $this->endDate = Carbon::now()->subDays(7);
        $this->secondDb = DB::connection('second_mysql');
        $this->thirdDb = DB::connection('third_mysql');
    }

    /**
     * realisasi
     */
    public function realisasi()
    {
        try {

            /**
             * aktivitas terbaru
             */
            $getPembayaranSppt = $this->pembayaranSppt->select(
                DB::raw("CONCAT(KD_PROPINSI , '.', KD_DATI2 , '.', KD_KECAMATAN,'.',KD_KELURAHAN ,'.',KD_BLOK ,'.',NO_URUT ,'.',KD_JNS_OP) AS nop"),
                "JML_SPPT_YG_DIBAYAR as nominal",
                DB::raw("DATE_FORMAT(TGL_PEMBAYARAN_SPPT,'%d/%m/%Y') as tanggal"),
                DB::raw('(SELECT NM_WP_SPPT FROM sppt WHERE CONCAT(sppt.KD_PROPINSI, sppt.KD_DATI2, sppt.KD_KECAMATAN, sppt.KD_KELURAHAN, sppt.KD_BLOK, sppt.NO_URUT, sppt.KD_JNS_OP) = 
                CONCAT(pembayaran_sppt.KD_PROPINSI, pembayaran_sppt.KD_DATI2, pembayaran_sppt.KD_KECAMATAN, pembayaran_sppt.KD_KELURAHAN, pembayaran_sppt.KD_BLOK, pembayaran_sppt.NO_URUT, pembayaran_sppt.KD_JNS_OP) ORDER BY sppt.THN_PAJAK_SPPT DESC limit 1) AS nama')
            )

                ->whereDate('TGL_PEMBAYARAN_SPPT', $this->startDate)
                ->orderByRaw('TGL_PEMBAYARAN_SPPT DESC, TGL_REKAM_BYR_SPPT DESC')
                ->limit(5)
                ->get();

            $getStsHistory = $this->secondDb->table('STS_History')->select('No_Pokok_WP AS nop', 'Nama_Pemilik AS nama')
                ->selectRaw("CONVERT(Nilai, SIGNED INTEGER) AS nominal")
                ->selectRaw("DATE_FORMAT(Tgl_Bayar,'%d/%m/%Y') as tanggal")
                ->whereDate('Tgl_Bayar', $this->startDate)
                ->where('Status_Bayar', 1)
                ->orderByDesc('Tgl_Bayar')
                ->limit(5)
                ->get();

            $getBriva = $this->thirdDb->table('briva_report')->select('custCode AS nop', 'nama')
                ->selectRaw("CONVERT(amount, SIGNED INTEGER) AS nominal")
                ->selectRaw("DATE_FORMAT(paymentDate,'%d/%m/%Y') as tanggal")
                ->whereDate('paymentDate', $this->startDate)
                ->orderByDesc('paymentDate')
                ->limit(5)
                ->get();

            $terbaru = [
                'pembayaran_sppt' => $getPembayaranSppt,
                'sts_history' => $getStsHistory,
                'briva' => $getBriva
            ];
            $totalTransaksiHariIni = $getPembayaranSppt->sum('nominal') + $getStsHistory->sum('nominal') + $getBriva->sum('nominal');

            /**
             * data chart
             */

            $chart = $this->pembayaranSppt
                ->select(
                    DB::raw('DATE_FORMAT(TGL_PEMBAYARAN_SPPT, "%d-%m-%Y") as tanggal'),
                    DB::raw('SUM(JML_SPPT_YG_DIBAYAR) as realisasi')
                )
                ->whereYear('TGL_PEMBAYARAN_SPPT', $this->year)
                ->whereBetween('TGL_PEMBAYARAN_SPPT', [$this->endDate, $this->startDate])
                ->groupBy(DB::raw('DATE(TGL_PEMBAYARAN_SPPT)'))
                ->orderBy('TGL_PEMBAYARAN_SPPT', 'DESC')
                ->get();

            /**
             * total bulan ini
             */
            $pbb = $this->pembayaranSppt
                ->select(DB::raw('COALESCE(SUM(JML_SPPT_YG_DIBAYAR), 0) AS total'))
                ->whereMonth('TGL_PEMBAYARAN_SPPT', $this->month)
                ->whereYear('TGL_PEMBAYARAN_SPPT', $this->year)
                ->first();
            $pajakLainnyaSts = $this->secondDb->table('STS_History')
                ->select(DB::raw('SUBSTRING(Jn_Pajak, 1, 5) AS Jn_Pajak'), 'Nm_Pajak', DB::raw('SUM(Nilai) AS total'))
                ->whereMonth('Tgl_Bayar', $this->month)
                ->whereYear('Tgl_Bayar', $this->year)
                ->groupBy(DB::raw('SUBSTRING(Jn_Pajak, 1, 5)'))
                ->get();
            $pajakLainnyaBri = $this->thirdDb->table('briva_report')
                ->select(DB::raw('SUBSTRING(custCode, 1, 5) AS Jn_Pajak'), DB::raw('SUM(amount) AS total'))
                ->whereMonth('date_created', $this->month)
                ->whereYear('date_created', $this->year)
                ->groupBy(DB::raw('SUBSTRING(custCode, 1, 5)'))
                ->get();
            $totalBulanIni = (int)$pbb->total + (int)round($pajakLainnyaSts->sum('total')) + (int)round($pajakLainnyaBri->sum('total'));

            /**
             * total semua
             */
            $pbb = $this->pembayaranSppt->select(DB::raw('COALESCE(SUM(JML_SPPT_YG_DIBAYAR), 0) AS total'))
                ->whereYear('TGL_PEMBAYARAN_SPPT', $this->year)
                ->first();
            $pajakLainnyaSts = $this->secondDb->table('STS_History')
                ->select(DB::raw('SUBSTRING(Jn_Pajak, 1, 5) AS Jn_Pajak'), 'Nm_Pajak', DB::raw('SUM(Nilai) AS total'))
                ->whereYear('Tgl_Bayar', $this->year)
                ->groupBy(DB::raw('SUBSTRING(Jn_Pajak, 1, 5)'))
                ->get();
            $pajakLainnyaBri = $this->thirdDb->table('briva_report')
                ->select(DB::raw('SUBSTRING(custCode, 1, 5) AS Jn_Pajak'), DB::raw('SUM(amount) AS total'))
                ->whereYear('date_created', $this->year)
                ->groupBy(DB::raw('SUBSTRING(custCode, 1, 5)'))
                ->get();
            $totalSemua = (int)$pbb->total + (int)round($pajakLainnyaSts->sum('total')) + (int)round($pajakLainnyaBri->sum('total'));

            /**
             * date perbulan
             */
            $dataBulan = $this->pembayaranSppt
                ->select(
                    DB::raw("YEAR(TGL_PEMBAYARAN_SPPT) AS tahun"),
                    DB::raw("MONTH(TGL_PEMBAYARAN_SPPT) AS bulan"),
                    DB::raw("SUM(JML_SPPT_YG_DIBAYAR) AS total")
                )
                ->whereYear('TGL_PEMBAYARAN_SPPT', $this->year)
                ->groupBy('bulan')
                ->get();
            // Inisialisasi array data per bulan
            $bulan = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            $dataPerbulan = [];

            // Inisialisasi total per bulan menjadi 0
            foreach ($bulan as $value) :
                $dataPerbulan[$value] = 0;
            endforeach;

            // Mengisi total per bulan dengan data yang ada
            foreach ($dataBulan as $item) :
                $bulan = $item['bulan'];
                $total = $item['total'];
                $dataPerbulan[$bulan] = $total;
            endforeach;

            // Format ulang data per bulan sesuai dengan struktur yang diinginkan
            $reformattedDataPerbulan = [];
            foreach ($dataPerbulan as $bulan => $total) :
                $reformattedDataPerbulan[] = ['bulan' => $bulan, 'total' => $total];
            endforeach;

            /**
             * table
             */
            $layanan = $this->layanan->get();
            $dataTable = [];
            foreach ($layanan as $key => $value) :

                /**
                 * pbb
                 */
                // $realiasi = $value->uuid_layanan == "9a1efe6c-921f-42d0-a969-92ba0e931472" ? (int) $totalSemua->realisasi : 0;
                $realiasi = 0;

                $set = [
                    'pajak' => $value->layanan,
                    'target' => 0,
                    'realisasi' => $realiasi,
                    'rkud' => 0
                ];
                array_push($dataTable, $set);
            endforeach;

            $data = [
                'totalHariIni' => $totalTransaksiHariIni,
                'totalBulanIni' => $totalBulanIni,
                'totalSemua' => $totalSemua,
                'terbaru' => $terbaru,
                'chart' => $chart,
                'table' => $dataTable,
                'perbulan' => $reformattedDataPerbulan
            ];
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Throwable $e) {
            $response  = $this->error($e->getMessage());
        }

        return $response;
    }
}
