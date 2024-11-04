<?php

namespace App\Repositories\PbbMinimal;

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

use App\Models\PbbMinimal\PbbMinimal;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\PbbMinimal\PbbMinimalRepositories;

class EloquentPbbMinimalRepositories implements PbbMinimalRepositories
{
    use Message, Response;

    private $pbbMinimal;
    private $checkerHelpers;

    public function __construct(
        PbbMinimal $pbbMinimal,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->pbbMinimal = $pbbMinimal;

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
            $data = $this->pbbMinimal->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get single data
     */
    public function get($thnPbbMinimal)
    {
        try {
            /**
             * helpers
             */

            $getPbbMinimal = $this->checkerHelpers->pbbMInimalChecker(['THN_PBB_MINIMAL' => $thnPbbMinimal]);
            if (is_null($getPbbMinimal)) :
                throw new \Exception($this->outputMessage('not found', 'PBB Minimal'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getPbbMinimal);
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
             * save data bidang
             */
            $saveData = $this->pbbMinimal->create($request);
            if ($saveData) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', 'PBB Minimal'));
            else :
                throw new \Exception($this->outputMessage('unsaved', 'PBB Minimal'));
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
    public function update($request, $thnPbbMinimal)
    {
        DB::beginTransaction();
        try {
            /**
             * validation data
             */
            $getPbbMinimal = $this->checkerHelpers->pbbMInimalChecker(['THN_PBB_MINIMAL' => $thnPbbMinimal]);
            if (is_null($getPbbMinimal)) :
                throw new \Exception($this->outputMessage('not found', 'PBB Minimal'));
            endif;

            /**
             * update data
             */
            $updatePbbMinimal = $this->pbbMinimal->where([
                'KD_PROPINSI' => globalAttribute()['kdProvinsi'],
                'KD_DATI2' => globalAttribute()['kdKota'],
                'THN_PBB_MINIMAL' => $thnPbbMinimal
            ])->update($request);

            if (!$updatePbbMinimal) :
                throw new \Exception($this->outputMessage('update fail', 'PBB Minimal'));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', 'PBB Minimal'));
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
    public function delete($thnPbbMinimal)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->pbbMInimalChecker(["THN_PBB_MINIMAL" => $thnPbbMinimal]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'PBB Minimal'));
            endif;

            /**
             * deleted data
             */
            $delete = $this->pbbMinimal->where('THN_PBB_MINIMAL', $thnPbbMinimal)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', 'PBB Minimal'));
            else :
                throw new \Exception($this->outputMessage('undeleted', 'PBB Minimal'));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
