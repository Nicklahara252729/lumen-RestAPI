<?php

namespace App\Repositories\JenisPerolehan;

/**
 * import component
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

use App\Models\JenisPerolehan\JenisPerolehan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\JenisPerolehan\JenisPerolehanRepositories;

class EloquentJenisPerolehanRepositories implements JenisPerolehanRepositories
{
    use Message, Response;

    private $signature;
    private $jenisPerolehan;
    private $checkerHelpers;

    public function __construct(
        JenisPerolehan $jenisPerolehan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->jenisPerolehan = $jenisPerolehan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * all record
     */
    public function data($pelayanan)
    {
        try {

            /**
             * data bidang
             */
            $data = $this->jenisPerolehan->where('pelayanan', $pelayanan)->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid
     */
    public function get($uuidJenisPerolehan)
    {
        try {
            /**
             * get single data
             */

            $getJenisPerolehan = $this->checkerHelpers->jenisPerolehanChecker(["uuid_jenis_perolehan" => $uuidJenisPerolehan]);
            if (is_null($getJenisPerolehan)) :
                throw new \Exception($this->outputMessage('not found', 'jenis perolehan'));
            endif;

            $response  = $this->successData($this->outputMessage('data', 1), $getJenisPerolehan);
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
             * save data bidang
             */
            $saveData = $this->jenisPerolehan->create($request);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', $request['jenis_perolehan']));
            endif;

            DB::commit();
            $response  = $this->success($this->outputMessage('saved', $request['jenis_perolehan']));
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
    public function update($request, $uuidJenisPerolehan)
    {
        DB::beginTransaction();
        try {
            /**
             * validation data
             */
            $getJenisPerolehan = $this->checkerHelpers->jenisPerolehanChecker(["uuid_jenis_perolehan" => $uuidJenisPerolehan]);
            if (is_null($getJenisPerolehan)) :
                throw new \Exception($this->outputMessage('not found', 'jenis perolehan'));
            endif;

            /**
             * update data
             */
            $updateBidang = $this->jenisPerolehan->where(['uuid_jenis_perolehan' => $uuidJenisPerolehan])->update($request);
            $req = isset($request['jenis_perolehan']) ? $request['jenis_perolehan'] : 'status jenis perolehan';
            if (!$updateBidang) :
                throw new \Exception($this->outputMessage('update fail', $req));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', $req));
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
    public function delete($uuidJenisPerolehan)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->jenisPerolehanChecker(["uuid_jenis_perolehan" => $uuidJenisPerolehan]);
            $jenisPerolehan  = is_null($getData) ? null : $getData->jenis_perolehan;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'jenis perolehan'));
            endif;

            /**
             * deleted data
             */
            $delete = $this->jenisPerolehan->where('uuid_jenis_perolehan', $uuidJenisPerolehan)->delete();
            if (!$delete) :
                throw new \Exception($this->outputMessage('undeleted', $jenisPerolehan));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('deleted', $jenisPerolehan));
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
