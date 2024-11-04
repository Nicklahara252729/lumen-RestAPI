<?php

namespace App\Http\Controllers\Sppt\Sppt;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Sppt\Sppt\SpptRepositories;
use App\Repositories\Log\LogRepositories;

class SpptController extends Controller
{
    use Message;

    private $request;
    private $signature;
    private $logRepositories;
    private $spptRepositories;

    public function __construct(
        Request $request,
        SpptRepositories $spptRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->spptRepositories = $spptRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->request = $request;
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * get all record data
     */
    public function data($pageSize = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->data($pageSize);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'sppt');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * riwayat pembayaran
     */
    public function history($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $kdJnsOp)
    {
        /**
         * load data from repositories
         */
        $request = isset($this->request->status) ? $this->request->status : null;
        $response = $this->spptRepositories->history($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $kdJnsOp, $request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'history sppt');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * search
     */
    public function search($kdKecamatan, $kdKelurahan, $nama = null, $kdBlok = null, $noUrut = null, $statusKolektif = null)
    {
        $nama = str_replace('%20', ' ', $nama);
        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->search($kdKecamatan, $kdKelurahan, $nama, $kdBlok, $noUrut, $statusKolektif);

        /**
         * save log
         */
        $log = $this->outputLogMessage(
            'search',
            'sppt',
            'kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' nama ' . $nama . ' kode blok ' . $kdBlok . ' no urut ' . $noUrut . ' status kolektif ' . $statusKolektif
        );
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * search by nop tahun
     */
    public function searchByNopTahun($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif, $tahun, $pageSize = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->searchByNopTahun($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif, $tahun, $pageSize);

        /**
         * save log
         */
        $log = $this->outputLogMessage(
            'search',
            'sppt',
            'kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' kode blok ' . $kdBlok . ' no urut ' . $noUrut . ' status kolektif ' . $statusKolektif . ' tahun ' . $tahun
        );
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * search
     */
    public function searchByKtp()
    {
        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->searchByKtp($this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('search', 'sppt', 'No KTP ' . $this->request->noKtp);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data blok nop
     */
    public function dataBlok($kdKecamatan = null, $kdKelurahan = null)
    {
        $role = authAttribute()['role'];
        $kdKecamatan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKecamatan : authAttribute()['kd_kecamatan'];
        $kdKelurahan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKelurahan : authAttribute()['kd_kelurahan'];

        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->dataBlok($kdKecamatan, $kdKelurahan);

        /**
         * save log
         */
        $moreValue = 'kode kecamatan : ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan;
        $log = $this->outputLogMessage('search', 'blok nop', $moreValue);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data nop by blok
     */
    public function dataNopByBlok($kdBlok, $kdKecamatan = null, $kdKelurahan = null)
    {
        $role = authAttribute()['role'];
        $kdKecamatan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKecamatan : authAttribute()['kd_kecamatan'];
        $kdKelurahan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKelurahan : authAttribute()['kd_kelurahan'];

        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->dataNopByBlok($kdKecamatan, $kdKelurahan, $kdBlok);

        /**
         * save log
         */
        $moreValue = 'kode kecamatan : ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' kode blok ' . $kdBlok;
        $log = $this->outputLogMessage('search', 'blok nop', $moreValue);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data blok nop selesai
     */
    public function dataBlokSelesai($kdKecamatan = null, $kdKelurahan = null, $uuidUser = null)
    {
        $role = authAttribute()['role'];
        $kdKecamatan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKecamatan : authAttribute()['kd_kecamatan'];
        $kdKelurahan = $role != 'petugas lapangan' && $role != 'kolektor' ? $kdKelurahan : authAttribute()['kd_kelurahan'];
        $uuidUser = $role != 'petugas lapangan' && $role != 'kolektor' ? $uuidUser : authAttribute()['id'];

        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->dataBlokSelesai($kdKecamatan, $kdKelurahan, $uuidUser);

        /**
         * save log
         */
        $moreValue = 'kode kecamatan : ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan;
        $log = $this->outputLogMessage('search', 'blok selesai', $moreValue);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data nop by blok selesai
     */
    public function dataNopByBlokSelesai($kdBlok = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->dataNopByBlokSelesai($kdBlok);

        /**
         * save log
         */
        $moreValue = ' kode blok ' . $kdBlok;
        $log = $this->outputLogMessage('search', 'blok nop selesai', $moreValue);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * riwayat pembayaran by nomor pelayanan
     */
    public function nopSelesai($nop)
    {
        /**
         * load data from repositories
         */
        $response = $this->spptRepositories->nopSelesai($nop);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'history sppt');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}
