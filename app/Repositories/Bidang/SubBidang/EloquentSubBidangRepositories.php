<?php

namespace App\Repositories\Bidang\SubBidang;

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

use App\Models\Bidang\SubBidang\SubBidang;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Bidang\SubBidang\SubBidangRepositories;


class EloquentSubBidangRepositories implements SubBidangRepositories
{
    use Message, Response;

    private $subBidang;
    private $checkerHelpers;

    public function __construct(
        SubBidang $subBidang,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->subBidang = $subBidang;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
    }

    /**
     * all record
     */
    public function data()
    {
        try {
            /**
             * data bidang
             */
            $data = $this->subBidang->select('sub_bidang.*', 'nama_bidang')
            ->join('bidang', 'sub_bidang.uuid_bidang', '=', 'bidang.uuid_bidang')
            ->get();

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
     * get data by uuid sub bidang
     */
    public function get($uuidSubBidang)
    {
        try {
            /**
             * helpers
             */

            $getSubBidang = $this->checkerHelpers->subBidangChecker(["uuid_sub_bidang" => $uuidSubBidang]);
            if (is_null($getSubBidang)) :
                throw new \Exception($this->outputMessage('not found', 'sub bidang'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getSubBidang);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid bidang
     */
    public function getByBidang($param)
    {
        try {
            /**
             * helpers
             */

            $data = $this->checkerHelpers->bidangInSubBidangChecker($param);

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
     * store data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $saveData = $this->subBidang->create($request);
            if ($saveData) :
                DB::commit();
                $response = [
                    "status" => TRUE,
                    "message" => $this->outputMessage('saved', $request['nama_sub_bidang'])
                ];
            else :
                throw new \Exception($this->outputMessage('unsaved', $request['nama_sub_bidang']));
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
    public function update($request, $uuidSubBidang)
    {        
        DB::beginTransaction();
        try {
            /**
             * update data
             */
            $updateBidang = $this->subBidang->where(['uuid_sub_bidang' => $uuidSubBidang])->update($request);

            if (!$updateBidang) :
                throw new \Exception($this->outputMessage('update fail', $request['nama_sub_bidang']));
            else :
                DB::commit();
                $response = [
                    "status"    => TRUE,
                    "message"   => $this->outputMessage('updated', $request['nama_sub_bidang'])
                ];
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
    public function delete($uuidSubBidang)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->subBidangChecker(["uuid_sub_bidang" => $uuidSubBidang]);
            $subBidang  = is_null($getData) ? null : $getData->nama_sub_bidang;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'sub bidang'));
            endif;

            /**
             * delete process
             */
            $delete = $this->subBidang->where('uuid_sub_bidang', $uuidSubBidang)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', $subBidang));
            else :
                throw new \Exception($this->outputMessage('undeleted', $subBidang));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
