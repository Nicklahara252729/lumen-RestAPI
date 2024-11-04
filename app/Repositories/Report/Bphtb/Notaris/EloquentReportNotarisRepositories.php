<?php

namespace App\Repositories\Report\Bphtb\Notaris;

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
use App\Traits\Converter;
use App\Traits\Calculation;

/**
 * import models
 */

use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;
use App\Models\JenisPerolehan\JenisPerolehan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Report\Bphtb\Notaris\ReportNotarisRepositories;
use App\Repositories\Pelayanan\Bphtb\EloquentBphtbRepositories;

class EloquentReportNotarisRepositories implements ReportNotarisRepositories
{
    use Message, Response, Converter, Calculation;

    private $pelayananBphtb;
    private $jenisPerolehan;
    private $checkerHelpers;
    private $secondDb;
    private $bphtbRepositories;

    public function __construct(
        CheckerHelpers $checkerHelpers,
        PelayananBphtb $pelayananBphtb,
        JenisPerolehan $jenisPerolehan,
        EloquentBphtbRepositories $bphtbRepositories
    ) {
        /**
         * initialize model
         */
        $this->pelayananBphtb = $pelayananBphtb;
        $this->jenisPerolehan = $jenisPerolehan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * initialize repositories
         */
        $this->bphtbRepositories = $bphtbRepositories;

        /**
         * static value
         */
        $this->secondDb = DB::connection('second_mysql');
    }

    /**
     * data
     */
    public function data($request)
    {
        try {

            /**
             * get notaris
             */
            $getNotaris = $this->checkerHelpers->userChecker(['uuid_user' => $request['uuid_user']]);
            $kode = is_null($getNotaris) ? "00" : $getNotaris->kode;
            $name = is_null($getNotaris) ? 'Semua Notaris' : $getNotaris->name;

            /**
             * convert tanggal
             */
            $startDate = Carbon::parse($request['start_date'])->locale('id');
            $startDate->settings(['formatFunction' => 'translatedFormat']);
            $endDate = Carbon::parse($request['end_date'])->locale('id');
            $endDate->settings(['formatFunction' => 'translatedFormat']);

            /**
             * status pembayaran
             */
            $statusPembayaran = $request['status_bayar'] == 'all' ? 'semua' : ($request['status_bayar'] == 1 ? 'sudah' : 'belum');

            /**
             * get jenis perolehan
             */
            $getJenisPerolehan = $this->checkerHelpers->jenisPerolehanChecker(['uuid_jenis_perolehan' => $request['uuid_jenis_perolehan']]);

            /**
             * where condition
             */
            $whereCreatedAt = $request['uuid_user'] != 'all' ? ['created_by' => $request['uuid_user']] : [];
            $whereJenisPerolehan = $request['uuid_jenis_perolehan'] != 'all' ? ['uuid_jenis_perolehan' => $request['uuid_jenis_perolehan']] : [];
            $where = array_merge($whereCreatedAt, $whereJenisPerolehan);
            $whereStatusBayar = $request['status_bayar'] == 'all' ? [] : ['Status_Bayar' => $request['status_bayar']];

            /**
             * get sspd
             */
            $getDataSspd = $this->pelayananBphtb->select(
                'nop',
                'created_at',
                'no_registrasi',
                'nama_wp_2',
                'luas_tanah',
                'luas_bangunan',
                'njop_tanah',
                'njop_bangunan',
                'npoptkp',
                'npop',
                'npopkp'
            )
                ->selectRaw('(SELECT jenis_perolehan FROM jenis_perolehan where uuid_jenis_perolehan = pelayanan_bphtb.uuid_jenis_perolehan) AS jenis_perolehan')
                ->where($where)
                ->whereRaw("DATE(created_at) between '" . $request['start_date'] . "' AND '" . $request['end_date'] . "'")
                ->get();
            $dataSspd = [];
            $total = 0;
            foreach ($getDataSspd as $key => $value) :
                $getStsHistory = $this->secondDb->table('STS_History')
                    ->select('Status_Bayar','Tgl_Bayar')
                    ->where(array_merge(['No_Pokok_Wp' => $value->nop], $whereStatusBayar))
                    ->first();
                if (!is_null($getStsHistory)) :

                    /**
                     * status bayar
                     */
                    $statusBayar = is_null($getStsHistory) ? 'belum' : ($getStsHistory->Status_Bayar == 0 ? 'belum' : 'sudah');

                    /**
                     * convert tanggal
                     */
                    $tanggal = Carbon::parse($value->created_at)->locale('id');
                    $tanggal->settings(['formatFunction' => 'translatedFormat']);
                    $tanggalBayar = Carbon::parse($getStsHistory->Tgl_Bayar)->locale('id');
                    $tanggalBayar->settings(['formatFunction' => 'translatedFormat']);

                    /** 
                     * detail bphtb
                     */
                    $npopkp = is_null($value->npopkp) || $value->npopkp <= 0 ? 0 : $value->npopkp;
                    $bphtbTerhutang = $this->bphtbTerhutang($npopkp);

                    /**
                     * get sts history
                     */
                    $set = [
                        'tanggal' => $tanggal->format('j F Y'),
                        'no_sspd' => $value->no_registrasi,
                        'nama_wp' => $value->nama_wp_2,
                        'nop' => $this->nopConvert($value->nop),
                        'luas_bumi_bangunan' => $value->luas_tanah . ' ' . $value->luas_bangunan,
                        'njop' => $value->njop_tanah + $value->njop_bangunan,
                        'perolehan' => $value->jenis_perolehan,
                        'npoptkp' => $value->npoptkp,
                        'npop' => $value->npop,
                        'npopkp' => $value->npopkp,
                        'bphtb' => $bphtbTerhutang,
                        'bayar' => $statusBayar,
                        'tanggal_bayar' => $tanggalBayar->format('j F Y'),
                    ];
                    array_push($dataSspd, $set);
                    $total += $bphtbTerhutang;
                endif;
            endforeach;

            $data = [
                'notaris' => $kode . ' - ' . $name,
                'start_date' => $startDate->format('j F Y'),
                'end_date' => $endDate->format('j F Y'),
                'status_bayar' => $statusPembayaran,
                'jenis_perolehan' => $request['uuid_jenis_perolehan'] == 'all' ? 'semua' : $getJenisPerolehan->jenis_perolehan,
                'total' => $total,
                'data_sspd' => $dataSspd
            ];
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
