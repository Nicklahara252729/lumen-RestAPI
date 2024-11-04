<?php

namespace App\Http\Controllers\Print;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * import form request
 */

use App\Http\Requests\Print\SpptRequest;
use App\Http\Requests\Print\SpptMasalMultipleRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Print\PrintRepositories;
use App\Repositories\Log\LogRepositories;

class PrintController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $printRepositories;

    public function __construct(
        Request $request,
        PrintRepositories $printRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize middleware
         */
        $this->middleware('auth:api', ['except' => ['permohonan']]);
        $this->middleware('signature', ['except' => ['permohonan']]);

        /**
         * initialize repositories
         */
        $this->printRepositories = $printRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * permohonan
     */
    public function permohonan($noPelayanan)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->permohonan($noPelayanan);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * surat keterangan njop
     */
    public function suratKeteranganNjop($param)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->suratKeteranganNjop($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'surat keterangan NJOP', 'No Pelayanan / NOP :' . $param);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * sppt
     */
    public function sppt(SpptRequest $spptRequest)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->sppt($spptRequest->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'SPPT', json_encode($spptRequest->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * sppt masal
     */
    public function spptMasal($kdKecamatan, $kdKelurahan, $kdBlok, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->spptMasal($kdKecamatan, $kdKelurahan, $kdBlok, $tahun, 'masal');

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'SPPT masal', 'kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' kode blok ' . $kdBlok . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * stts
     */
    public function stts($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->stts($kdKecamatan, $kdKelurahan, $kdBlok, $noUrut, $statusKolektif, $tahun);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'STTS', 'kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' kode blok ' . $kdBlok . ' status kolektif ' . $statusKolektif . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * sppt buku 45
     */
    public function spptBuku45($kdKecamatan, $kdKelurahan, $kdBlok, $tahun)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->spptMasal($kdKecamatan, $kdKelurahan, $kdBlok, $tahun, 'buku 45');

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'SPPT masal', 'kode kecamatan ' . $kdKecamatan . ' kode kelurahan ' . $kdKelurahan . ' kode blok ' . $kdBlok . ' tahun ' . $tahun);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * sppt masal multiple
     */
    public function spptMasalMultiple(SpptMasalMultipleRequest $request)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->spptMasalMultiple($request->all());

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'SPPT', json_encode($request->all()));
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * sspd
     */
    public function sspd($uuidPelayananBphtb)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->sspd($uuidPelayananBphtb);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'BPHTB');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * skpdkb
     */
    public function skpdkb($sspd)
    {
        /**
         * load data from repositories
         */
        $response = $this->printRepositories->skpdkb($sspd);

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'BPHTB');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}
