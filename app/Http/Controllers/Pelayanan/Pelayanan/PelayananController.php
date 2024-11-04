<?php

namespace App\Http\Controllers\Pelayanan\Pelayanan;

/**
 * import component
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

use App\Repositories\Pelayanan\Pelayanan\PelayananRepositories;
use App\Repositories\Log\LogRepositories;
use App\Repositories\Pelayanan\Pbb\PetaObjekPajak\PetaObjekPajakRepositories;
use App\Repositories\Pelayanan\Pbb\Mutasi\MutasiRepositories;
use App\Repositories\Sppt\PembayaranManual\PembayaranManualRepositories;
use App\Repositories\Pelayanan\Pbb\GabungNop\GabungNopRepositories;
use App\Repositories\Pelayanan\Pbb\PecahNop\PecahNopRepositories;
use App\Repositories\Pelayanan\Pbb\Lspop\LspopRepositories;

class PelayananController extends Controller
{
    use Message;

    private $signature;
    private $request;
    private $logRepositories;
    private $pelayananRepositories;
    private $petaObjekPajakRepositories;
    private $mutasiRepositories;
    private $pembayaranManualRepositories;
    private $gabungNopRepositories;
    private $pecahNopRepositories;
    private $lspopRepositories;

    public function __construct(
        Request $request,
        PelayananRepositories $pelayananRepositories,
        LogRepositories $logRepositories,
        PetaObjekPajakRepositories $petaObjekPajakRepositories,
        MutasiRepositories $mutasiRepositories,
        PembayaranManualRepositories $pembayaranManualRepositories,
        GabungNopRepositories $gabungNopRepositories,
        PecahNopRepositories $pecahNopRepositories,
        LspopRepositories $lspopRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->pelayananRepositories = $pelayananRepositories;
        $this->logRepositories = $logRepositories;
        $this->petaObjekPajakRepositories = $petaObjekPajakRepositories;
        $this->mutasiRepositories = $mutasiRepositories;
        $this->pembayaranManualRepositories = $pembayaranManualRepositories;
        $this->gabungNopRepositories = $gabungNopRepositories;
        $this->pecahNopRepositories = $pecahNopRepositories;
        $this->lspopRepositories = $lspopRepositories;

        /**
         * initialize component
         */
        $this->request = $request;
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * generate
     */
    public function generate()
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->generate();

        /**
         * save log
         */
        $log = $this->outputLogMessage('generate', 'nomor pelayanan', json_encode($response));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show all data
     */
    public function data($param, $pageSize = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->data($param, $pageSize);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'pelyanan', 'by parameter ' . $param);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show data realisasi by kecamatan
     */
    public function dataRealisasiKecamatan($kdKecamatan, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->dataRealisasiKecamatan($kdKecamatan, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'realisasi kecamatan', 'by kode kecamatan ' . $kdKecamatan . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show data piutang by kecamatan
     */
    public function dataPiutangKecamatan($kdKecamatan, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->dataPiutangKecamatan($kdKecamatan, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'piutang kecamatan', 'by kode kecamatan ' . $kdKecamatan . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show data jumlah wp by kecamatan
     */
    public function dataJumlahWpKecamatan($kdKecamatan, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->dataJumlahWpKecamatan($kdKecamatan, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'jumlah wp kecamatan', 'by kode kecamatan ' . $kdKecamatan . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);


        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show data realisasi by kelurahan
     */
    public function dataRealisasiKelurahan($kdKecamatan, $kdKelurahan, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->dataRealisasiKelurahan($kdKecamatan, $kdKelurahan, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'realisasi kelurahan', 'by kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show data piutang by kelurahan
     */
    public function dataPiutangKelurahan($kdKecamatan, $kdKelurahan, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->dataPiutangKelurahan($kdKecamatan, $kdKelurahan, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'piutang kelurahan', 'by kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * show data jumlah wp by kelurahan
     */
    public function dataJumlahWpKelurahan($kdKecamatan, $kdKelurahan, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->dataJumlahWpKelurahan($kdKecamatan, $kdKelurahan, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'jumlah wp kelurahan', 'by kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * search data
     */
    public function search($param, $pageSize = null)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->search($param, $pageSize, $this->request);

        /**
         * save log
         */
        $log = $this->outputLogMessage('search', 'pelyanan', $param . ' dan ' . json_encode($this->request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * count sppt
     */
    public function countSppt($kdKecamatan, $kdKelurahan, $kdBlok, $noUrutAwal, $noUrutAkhir, $statusKolektif)
    {
        /**
         * load data from repositories
         */
        $response = $this->pelayananRepositories->countSppt($kdKecamatan, $kdKelurahan, $kdBlok, $noUrutAwal, $noUrutAkhir, $statusKolektif);

        /**
         * save log
         */
        $log = $this->outputLogMessage('count', 'sppt');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * autocomplete
     */
    public function autocomplete($pelayanan, $nop, $tahun = null)
    {
        /**
         * load data from repositories
         */
        if ($pelayanan == 'peta-nop') :
            $response = $this->petaObjekPajakRepositories->autocomplete($nop);
        elseif ($pelayanan == 'pecah-nop') :
            $response = $this->pecahNopRepositories->autocomplete($nop);
        elseif ($pelayanan == 'gabung-nop') :
            $response = $this->gabungNopRepositories->autocomplete($nop, $tahun);
        elseif ($pelayanan == 'mutasi-objek') :
            $response = $this->mutasiRepositories->autocompleteObjek($nop, $tahun);
        elseif ($pelayanan == 'mutasi-subjek') :
            $response = $this->mutasiRepositories->autocompleteSubjek($nop, $tahun);
        elseif ($pelayanan == 'koreksi-pembayaran') :
            $response = $this->pembayaranManualRepositories->autocomplete($nop, $tahun);
        elseif ($pelayanan == 'lspop') :
            $response = $this->lspopRepositories->autocomplete($nop, $tahun);
        else :
            $response = ['status' => false, 'message' => 'autocomplete tidak ada'];
        endif;

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'autocomplete', 'NOP :' . $nop);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * count nop
     */
    public function countNop()
    {
        /**
         * load data from repositories
         */
        $kdKecamatan = $this->request->get('kdKecamatan');
        $kdKelurahan = $this->request->get('kdKelurahan');
        $response = $this->pelayananRepositories->countNop($kdKecamatan, $kdKelurahan);

        /**
         * save log
         */
        $log = $this->outputLogMessage('count', 'sppt');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}
