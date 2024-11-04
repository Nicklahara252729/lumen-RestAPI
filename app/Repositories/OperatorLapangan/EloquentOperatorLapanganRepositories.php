<?php

namespace App\Repositories\OperatorLapangan;

/**
 * import component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;
use App\Libraries\PaginateHelpers;

/**
 * import interface
 */

use App\Repositories\OperatorLapangan\OperatorLapanganRepositories;

class EloquentOperatorLapanganRepositories implements OperatorLapanganRepositories
{
    use Message, Response, Generator;

    private $checkerHelpers;
    private $paginateHelpers;
    private $thirdDb;
    private $nopd;
    private $stsHistory;
    private $year;
    private $datetime;
    private $regPribadi;
    private $objekPajak;
    private $storage;
    private $hiburan;
    private $hotel;
    private $parkir;
    private $pat;
    private $penerangan;
    private $pln;
    private $reklame;
    private $walet;

    public function __construct(
        CheckerHelpers $checkerHelpers,
        PaginateHelpers $paginateHelpers
    ) {

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->paginateHelpers = $paginateHelpers;

        /**
         * static value
         */
        $this->thirdDb = DB::connection('third_mysql');
        $this->nopd = $this->thirdDb->table('x_nopd');
        $this->regPribadi = $this->thirdDb->table('x_regpribadi');
        $this->year = Carbon::now()->format('Y');
        $this->datetime = Carbon::now()->toDateTimeLocalString();
        $this->objekPajak = $this->thirdDb->table('x_objek_pajak');
        $this->storage = path('operator lapangan');
        $this->hiburan = $this->thirdDb->table('x_hiburan');
        $this->hotel = $this->thirdDb->table('x_hotel');
        $this->parkir = $this->thirdDb->table('x_parkir');
        $this->pat = $this->thirdDb->table('x_pat');
        $this->penerangan = $this->thirdDb->table('x_penerangan');
        $this->pln = $this->thirdDb->table('x_pln');
        $this->reklame = $this->thirdDb->table('x_reklame');
        $this->walet = $this->thirdDb->table('x_walet');
    }

    /**
     * get all record
     */
    public function data()
    {
        try {

            $uuidUser = authAttribute()['id'];
            $data = $this->objekPajak->select(
                "id",
                "nopd",
                "nama_objek",
                "nama_pemilik",
                "alamat_objek",
                "alamat_pemilik",
                "latitude",
                "longitude",
                "updated_at"
            )
                ->selectRaw('CASE WHEN photo IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", photo) END AS photo')
                ->where('updated_by', $uuidUser)
                ->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak hiburan
     */
    public function dataHiburan()
    {
        try {

            $data = $this->hiburan->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak hotel
     */
    public function dataHotel()
    {
        try {

            $data = $this->hotel->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak parkir
     */
    public function dataParkir()
    {
        try {

            $data = $this->parkir->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak pat
     */
    public function dataPat()
    {
        try {

            $data = $this->pat->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak penerangan
     */
    public function dataPenerangan()
    {
        try {

            $data = $this->penerangan->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak pln
     */
    public function dataPln()
    {
        try {

            $data = $this->pln->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak reklame
     */
    public function dataReklame()
    {
        try {

            $data = $this->reklame->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record pajak walet
     */
    public function dataWalet()
    {
        try {

            $data = $this->walet->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get autocomplete
     */
    public function autocomplete($key)
    {
        try {

            $getData = $this->nopd->select(
                'nopd',
                'nama_pemilik',
                'nama_usaha AS nama_objek',
                'x_nopd.alamat AS alamat_objek',
                'x_regpribadi.alamat AS alamat_pemilik'
            )
                ->join('x_regpribadi', 'x_nopd.npwpd', '=', 'x_regpribadi.npwpd')
                ->where(function ($query) use ($key) {
                    $query->where('x_nopd.nopd', $key)
                        ->orWhere('x_regpribadi.nama_pemilik', 'like', '%' . $key . '%')
                        ->orWhere('x_nopd.npwpd', $key);
                })
                ->first();
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'NOPD'));
            endif;

            $response = $this->successData($this->outputMessage('data', 1), $getData);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * store data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {

            /**
             * check data
             */
            $checkData = $this->objekPajak->where('nopd', $request['nopd'])->first();
            if (!is_null($checkData)) :
                throw new \Exception($this->outputMessage('exists', $request['nopd']));
            endif;

            /**
             * uploading files
             */
            if (isset($_FILES['photo'])) :
                $photoName = $_FILES['photo']['name'];
                $photoTempName = $_FILES['photo']['tmp_name'];
                $photoExt = explode('.', $photoName);
                $photoActualExt = strtolower(end($photoExt));
                $photoNew = Uuid::uuid4()->getHex() . "." . $photoActualExt;
                $photoDestination = $this->storage . '/' . $photoNew;
                if (!move_uploaded_file($photoTempName, $photoDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $request['photo'] = $photoNew;
            endif;

            /**
             * saving process
             */
            $request['status'] = 'lama';
            $saveData = $this->objekPajak->insert($request);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', 'Objek Pajak dengan NOPD ' . $request['nopd']));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'Objek Pajak dengan NOPD ' . $request['nopd']));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * store reg pribadi data to db
     */
    public function storeRegpribadi($request)
    {
        DB::beginTransaction();
        try {

            /**
             * saving process
             */
            $input = collect($request)->except(['nama_usaha', 'jenis_pajak'])->toArray();
            $input['jenis'] = $request['jenis_pajak'];
            $input['nama'] = $request['nama_usaha'];
            $saveData = $this->regPribadi->insert($input);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', 'Reg Prbadi'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'Reg Prbadi'));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * store nopd data to db
     */
    public function storeNopd($request)
    {
        DB::beginTransaction();
        try {

            /**
             * set input nopd request
             */
            $nopdInput = collect($request)->only(['npwpd', 'nopd', 'nama_usaha', 'alamat', 'kecamatan_id', 'kelurahan_id'])->toArray();
            $nopdInput['created_by'] = $request['updated_by'];
            $nopdInput['date_created'] = $request['updated_at'];

            /**
             * uploading files
             */
            $opInput = collect($request)->only(['latitude', 'longitude', 'nopd', 'updated_by', 'updated_at'])->toArray();
            $opInput['nama_objek'] = $request['nama_usaha'];
            $opInput['status'] = 'baru';
            if (isset($_FILES['photo'])) :
                $photoName = $_FILES['photo']['name'];
                $photoTempName = $_FILES['photo']['tmp_name'];
                $photoExt = explode('.', $photoName);
                $photoActualExt = strtolower(end($photoExt));
                $photoNew = Uuid::uuid4()->getHex() . "." . $photoActualExt;
                $photoDestination = $this->storage . '/' . $photoNew;
                if (!move_uploaded_file($photoTempName, $photoDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;
                $opInput['photo'] = $photoNew;
            endif;

            /**
             * saving to nopd process
             */
            $saveNopd = $this->nopd->insert($nopdInput);
            if (!$saveNopd) :
                throw new \Exception($this->outputMessage('unsaved', 'NOPD'));
            endif;

            /**
             * saving to objek pajak process
             */
            $saveObjekPajak = $this->objekPajak->insert($opInput);
            if (!$saveObjekPajak) :
                throw new \Exception($this->outputMessage('unsaved', 'Objek Pajak'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('saved', 'NOPD'));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * get all record reg pribadi
     */
    public function dataRegpribadi()
    {
        try {

            $uuidUser = authAttribute()['id'];
            $data = $this->regPribadi->select(
                'regpribadi_id AS id',
                'npwpd',
                'nama AS nama_usaha',
                'nama_pemilik',
                'alamat',
                'no_telp',
                'date_created',
                'ref_kecamatan.nm_kecamatan AS kecamatan',
                'ref_kelurahan.nm_kelurahan AS kelurahan'
            )
                ->leftJoin('ref_kecamatan', 'x_regpribadi.kecamatan_id', '=', 'ref_kecamatan.kdkecamatan')
                ->leftJoin('ref_kelurahan', function ($join) {
                    $join->on('x_regpribadi.kecamatan_id', '=', 'ref_kelurahan.kdkecamatan')
                        ->on('x_regpribadi.kelurahan_id', '=', 'ref_kelurahan.no_kelurahan');
                })
                ->where('created_by', $uuidUser)
                ->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get all record nopd
     */
    public function dataNopd()
    {
        try {

            $uuidUser = authAttribute()['id'];
            $data = $this->nopd->select(
                "nopd_id AS id",
                "npwpd",
                "x_nopd.nopd",
                "nama_usaha",
                "alamat",
                'ref_kecamatan.nm_kecamatan AS kecamatan',
                'ref_kelurahan.nm_kelurahan AS kelurahan',
                "date_created",
                "latitude",
                "longitude",
            )
                ->selectRaw('CASE WHEN photo IS NULL THEN NULL ELSE CONCAT("' . url($this->storage) . '/", photo) END AS photo')
                ->join('x_objek_pajak', 'x_nopd.nopd', '=', 'x_objek_pajak.nopd')
                ->leftJoin('ref_kecamatan', 'x_nopd.kecamatan_id', '=', 'ref_kecamatan.kdkecamatan')
                ->leftJoin('ref_kelurahan', function ($join) {
                    $join->on('x_nopd.kecamatan_id', '=', 'ref_kelurahan.kdkecamatan')
                        ->on('x_nopd.kelurahan_id', '=', 'ref_kelurahan.no_kelurahan');
                })
                ->where('created_by', $uuidUser)
                ->get();
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $response  = $this->successData($this->outputMessage('data', count($dataPaginate)), $dataPaginate);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }


    /**
     * search
     */
    public function search($key, $idKecamatan, $idKelurahan)
    {
        try {
            $indicator = substr($key, 0, 1);
            $query = $this->nopd->select(
                "x_nopd.npwpd",
                "nopd",
                "nama_usaha",
                "x_nopd.alamat AS alamat_usaha",
                'nama_pemilik',
                'x_regpribadi.alamat AS alamat_pemilik'
            )
                ->join('x_regpribadi', 'x_nopd.npwpd', '=', 'x_regpribadi.npwpd')
                ->where(['x_nopd.kecamatan_id' => $idKecamatan, 'x_nopd.kelurahan_id' => $idKelurahan]);
            if ($indicator == 'P') :
                $npwpd = true;
                $data = $query->where('x_nopd.npwpd', $key)->limit(1)->get();
            else :
                $npwpd = false;
                $data = $query->where(function ($query) use ($key) {
                    $query->where('nopd', $key)
                        ->orWhere('nama_usaha', 'like', '%' . $key . '%');
                })
                    ->get();
            endif;
            $collectionObject = collect($data);
            $dataPaginate = $this->paginateHelpers->paginate($collectionObject, 10);

            $result  = $this->successData($this->outputMessage('data', count($data)), $dataPaginate);
            $response = array_merge(['npwpd' => $npwpd], $result);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
