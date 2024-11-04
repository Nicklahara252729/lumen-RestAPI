<?php

namespace App\Repositories\Layanan\JenisLayanan;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Layanan\JenisLayanan\JenisLayanan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Layanan\JenisLayanan\JenisLayananRepositories;

class EloquentJenisLayananRepositories implements JenisLayananRepositories
{
    use Message, Response;

    private $jenisLayanan;
    private $checkerHelpers;

    public function __construct(
        JenisLayanan $jenisLayanan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->jenisLayanan = $jenisLayanan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * all record
     */
    public function data($status)
    {
        try {
            /**
             * data jenis layanan
             */
            $data = $this->jenisLayanan;
            if ($status != 'all')
                $data = $data->where('status', $status);
            $data = $data->get();

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid jenis layanan
     */
    public function get($uuidJenisLayanan)
    {
        try {
            /**
             * helpers
             */

            $getJenisLayanan = $this->checkerHelpers->jenisLayananChecker(["uuid_jenis_layanan" => $uuidJenisLayanan]);
            if (is_null($getJenisLayanan)) :
                throw new \Exception($this->outputMessage('not found', 'jenis layanan'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getJenisLayanan);
            endif;
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
             * save data layanan
             */
            $saveData = $this->jenisLayanan->create($request);
            if ($saveData) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', $request['jenis_layanan']));
            else :
                throw new \Exception($this->outputMessage('unsaved', $request['jenis_layanan']));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * update data to db
     */
    public function update($request, $uuidJenisLayanan)
    {
        DB::beginTransaction();
        try {
            /**
             * validation data
             */
            $getJenisLayanan = $this->checkerHelpers->jenisLayananChecker(["uuid_jenis_layanan" => $uuidJenisLayanan]);
            if (is_null($getJenisLayanan)) :
                throw new \Exception($this->outputMessage('not found', 'jenis layanan'));
            endif;

            /**
             * update data
             */
            $updateJenisLayanan = $this->jenisLayanan->where(['uuid_jenis_layanan' => $uuidJenisLayanan])->update($request);

            if (!$updateJenisLayanan) :
                throw new \Exception($this->outputMessage('update fail', $request['jenis_layanan']));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', $request['jenis_layanan']));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }

        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * delete data from db
     */
    public function delete($uuidJenisLayanan)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->jenisLayananChecker(["uuid_jenis_layanan" => $uuidJenisLayanan]);
            $jenisLayanan  = is_null($getData) ? null : $getData->jenis_layanan;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'jenis layanan'));
            endif;

            /**
             * process delete
             */
            $delete = $this->jenisLayanan->where('uuid_jenis_layanan', $uuidJenisLayanan)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', $jenisLayanan));
            else :
                throw new \Exception($this->outputMessage('undeleted', $jenisLayanan));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
