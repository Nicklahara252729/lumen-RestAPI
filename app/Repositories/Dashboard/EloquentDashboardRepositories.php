<?php

namespace App\Repositories\Dashboard;

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

use App\Models\Pelayanan\Pelayanan\Pelayanan;
use App\Models\Layanan\Layanan\Layanan;
use App\Models\PembayaranSppt\PembayaranSppt\PembayaranSppt;
use App\Models\Sppt\Sppt;
use App\Models\Pelayanan\PelayananBphtb\PelayananBphtb;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Dashboard\DashboardRepositories;

class EloquentDashboardRepositories implements DashboardRepositories
{
    use Message, Response;

    private $pelayanan;
    private $layanan;
    private $sppt;
    private $pembayaranSppt;
    private $checkerHelpers;
    private $year;
    private $idUser;
    private $roleUser;
    private $pelayananBphtb;

    public function __construct(
        Pelayanan $pelayanan,
        Layanan $layanan,
        Sppt $sppt,
        PembayaranSppt $pembayaranSppt,
        PelayananBphtb $pelayananBphtb,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->pelayanan = $pelayanan;
        $this->layanan = $layanan;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->sppt = $sppt;
        $this->pelayananBphtb = $pelayananBphtb;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->year     = Carbon::now()->format('Y');
        $this->idUser   = authAttribute()['id'];
        $this->roleUser = authAttribute()['role'];
    }

    /**
     * total permohonan
     */
    public function totalPermohonan()
    {
        try {
            /**
             * data bidang
             */
            $data = $this->pelayanan->select(DB::raw('
            COUNT(*) AS total, 
            SUM(CASE WHEN status_verifikasi = 1 THEN 1 ELSE 0 END) AS baru,
            SUM(CASE WHEN status_verifikasi = 2 AND role_reject = "'.$this->roleUser.'" THEN 1 ELSE 0 END) AS ditolak,
            SUM(CASE WHEN status_verifikasi = 3 THEN 1 ELSE 0 END) AS diverifikasi,
	        SUM(CASE WHEN status_verifikasi = 4 THEN 1 ELSE 0 END) AS ditetapkan
            '))->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * total per layanan
     */
    public function totalPerLayanan()
    {
        try {
            /**
             * data bidang
             */
            $data = $this->layanan->select(DB::raw('
            layanan,
            COUNT(layanan) AS total, 
            SUM(CASE WHEN status_verifikasi = 1 THEN 1 ELSE 0 END) AS baru,
            SUM(CASE WHEN status_verifikasi = 2 THEN 1 ELSE 0 END) AS ditolak,
            SUM(CASE WHEN status_verifikasi = 3 THEN 1 ELSE 0 END) AS diverifikasi,
	        SUM(CASE WHEN status_verifikasi = 4 THEN 1 ELSE 0 END) AS ditetapkan
            '))
                ->join('pelayanan', 'layanan.uuid_layanan', '=', 'pelayanan.uuid_layanan')
                ->groupBy('layanan.uuid_layanan')
                ->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * data by kecamatan & kelurahan
     */
    public function dataByKecamatanOrKelurahan($kdKecamatan, $kdKelurahan)
    {
        try {
            if (is_null($kdKelurahan) || empty($kdKelurahan)) :
                $where = ['KD_KECAMATAN' => $kdKecamatan];
                $wherePelayanan = ['op_kd_kecamatan' => $kdKecamatan];
                $selectJumlahWp = '(SELECT COUNT(*) FROM sppt WHERE THN_PAJAK_SPPT = YEAR(NOW()) AND KD_KECAMATAN = ' . $kdKecamatan . ') AS jumlah_wp';
            else :
                $where = [
                    'KD_KECAMATAN' => $kdKecamatan,
                    'KD_KELURAHAN' => $kdKelurahan
                ];
                $wherePelayanan = [
                    'op_kd_kecamatan' => $kdKecamatan,
                    'op_kd_kelurahan' => $kdKelurahan
                ];
                $selectJumlahWp = '(SELECT COUNT(*) FROM sppt WHERE THN_PAJAK_SPPT = YEAR(NOW()) AND KD_KECAMATAN = ' . $kdKecamatan . ' AND KD_KELURAHAN = ' . $kdKelurahan . ') AS jumlah_wp';
            endif;
            $dataPelayanan = $this->pelayanan->select(DB::raw('
            COUNT(*) AS total, 
            SUM(CASE WHEN status_verifikasi = 1 THEN 1 ELSE 0 END) AS baru,
            SUM(CASE WHEN status_verifikasi = 2 THEN 1 ELSE 0 END) AS ditolak,
            SUM(CASE WHEN status_verifikasi = 3 THEN 1 ELSE 0 END) AS diverifikasi,
	        SUM(CASE WHEN status_verifikasi = 4 THEN 1 ELSE 0 END) AS ditetapkan
            '))
                ->where($wherePelayanan)
                ->first();

            $getSppt = $this->sppt->select(DB::raw($selectJumlahWp . ',SUM(PBB_TERHUTANG_SPPT) AS piutang'))
                ->where($where)
                ->first();

            $dataPembayaranSppt = $this->pembayaranSppt->select(DB::raw('SUM(JML_SPPT_YG_DIBAYAR) AS realisasi'))
                ->where(array_merge($where, ['THN_PAJAK_SPPT' => $this->year]))
                ->first();

            $data = [
                'total' => $dataPelayanan->total,
                'baru' => $dataPelayanan->baru,
                'ditolak' => $dataPelayanan->ditolak,
                'diverifikasi' => $dataPelayanan->diverifikasi,
                'ditetapkan' => $dataPelayanan->ditetapkan,
                'jumlah_wp' => $getSppt->jumlah_wp,
                'piutang' => $getSppt->piutang,
                'realisasi' => $dataPembayaranSppt->realisasi,
            ];

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', 1), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * total permohonan bphtb
     */
    public function totalPermohonanBphtb()
    {
        try {
            /**
             * data bidang
             */
            $data = $this->pelayananBphtb->select(DB::raw('
            COUNT(*) AS total, 
            SUM(CASE WHEN status_verifikasi = 0 THEN 1 ELSE 0 END) AS belum_verifikasi,
            SUM(CASE WHEN status_verifikasi = 1 THEN 1 ELSE 0 END) AS verifikasi_operator,
            SUM(CASE WHEN status_verifikasi = 2 THEN 1 ELSE 0 END) AS validasi_kasubid,
	        SUM(CASE WHEN status_verifikasi = 3 THEN 1 ELSE 0 END) AS ditetapkan_kabid,
            SUM(CASE WHEN status_verifikasi = 4 THEN 1 ELSE 0 END) AS ditolak
            '))->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
