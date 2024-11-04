<?php

namespace App\Repositories\PetaObjekPajak;

/**
 * default component
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
use App\Models\TagihanKolektor\TagihanKolektor;
use App\Models\View\PetaObjekPajak\PetaObjekPajak;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\PetaObjekPajak\PetaObjekPajakRepositories;

class EloquentPetaObjekPajakRepositories implements PetaObjekPajakRepositories
{
    use Message, Response, Generator, Calculation;

    private $sppt;
    private $pembayaranSppt;
    private $datObjekPajak;
    private $checkerHelpers;
    private $paginateHelpers;
    private $provinsi;
    private $kabupaten;
    private $tagihanKolektor;
    private $datetime;
    private $year;
    private $viewPetaObjekPajak;
    private $storage;

    public function __construct(
        Sppt $sppt,
        TagihanKolektor $tagihanKolektor,
        PembayaranSppt $pembayaranSppt,
        DatObjekPajak $datObjekPajak,
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers,
        PetaObjekPajak $viewPetaObjekPajak
    ) {
        /**
         * initialize model
         */
        $this->sppt = $sppt;
        $this->pembayaranSppt = $pembayaranSppt;
        $this->datObjekPajak = $datObjekPajak;
        $this->tagihanKolektor = $tagihanKolektor;
        $this->viewPetaObjekPajak = $viewPetaObjekPajak;

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
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->year = Carbon::now()->format('Y');
        $this->storage = path('tunggakan');
    }

    /**
     * get all record
     */
    public function data($kdKecamatan, $kdKelurahan, $blok)
    {
        try {
            if ($kdKecamatan != 'all' & $kdKelurahan == null && $blok == null) :
                $where = ['KD_KECAMATAN' => $kdKecamatan];
            elseif ($kdKecamatan != 'all' & !is_null($kdKelurahan) && $blok == null) :
                $where = ['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan];
            elseif ($kdKecamatan != 'all' & !is_null($kdKelurahan) && !is_null($blok)) :
                $where = ['KD_KECAMATAN' => $kdKecamatan, 'KD_KELURAHAN' => $kdKelurahan, 'KD_BLOK' => $blok];
            endif;

            /**
             * get data
             */
            $dataPetaOp = $this->viewPetaObjekPajak->select('NM_WP_SPPT AS nama_wp', 'latitude', 'longitude', 'alamat_op', 'updated_at', 'updated_by')
                ->selectRaw("CONCAT(KD_PROPINSI,'.',KD_DATI2,'.',KD_KECAMATAN,'.',KD_KELURAHAN,'.',KD_BLOK,'.',NO_URUT,'.',KD_JNS_OP) AS nop")
                ->selectRaw('CONCAT("' . url($this->storage) . '/", photo) AS photo');
            if ($kdKecamatan != 'all') :
                $dataPetaOp->where($where);
            endif;
            $dataPetaOp = $dataPetaOp->get();
            $data = [];
            foreach ($dataPetaOp as $key => $value) :
                /**
                 * tanggal update
                 */
                $tanggalUpdate = Carbon::parse($value->updated_at)->locale('id');
                $tanggalUpdate->settings(['formatFunction' => 'translatedFormat']);

                $set = [
                    'nop'           => $value->nop,
                    'nama_wp'       => $value->nama_wp,
                    'latitude'      => $value->latitude,
                    'longitude'     => $value->longitude,
                    'alamat_op'     => $value->alamat_op,
                    'updated_at'    => $tanggalUpdate->format('j F Y, H:i:s'),
                    'photo'         => $value->photo,
                    'updated_by'    => $value->updated_by,
                ];
                array_push($data, $set);
            endforeach;

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
