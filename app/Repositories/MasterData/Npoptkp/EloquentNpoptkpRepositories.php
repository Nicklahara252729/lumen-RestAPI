<?php

namespace App\Repositories\MasterData\Npoptkp;

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

use App\Models\MasterData\Npoptkp\Npoptkp;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\MasterData\Npoptkp\NpoptkpRepositories;

class EloquentNpoptkpRepositories implements NpoptkpRepositories
{
    use Message, Response;

    private $npoptkp;
    private $checkerHelpers;

    public function __construct(
        Npoptkp $npoptkp,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->npoptkp = $npoptkp;

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
             * data npotkp
             */
            $data = $this->npoptkp->get();

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
     * get data by uuid npotkp
     */
    public function get($uuidNpoptkp)
    {
        try {
            /**
             * helpers
             */

            $getNpoptkp = $this->checkerHelpers->npoptkpChecker(["uuid_npoptkp" => $uuidNpoptkp]);
            if (is_null($getNpoptkp)) :
                throw new \Exception($this->outputMessage('not found', 'npotkp'));
            endif;
            
            $response  = $this->successData($this->outputMessage('data', 1), $getNpoptkp);
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
            $saveData = $this->npoptkp->create($request);
            $npoptkp = 'Tahun '.$request['tahun'];
            if ($saveData) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', $npoptkp));
            else :
                throw new \Exception($this->outputMessage('unsaved', $npoptkp));
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
    public function update($request, $uuidNpoptkp)
    {
        DB::beginTransaction();
        try {
            /**
             * validation data
             */
            $getNpoptkp = $this->checkerHelpers->npoptkpChecker(["uuid_npoptkp" => $uuidNpoptkp]);
            if (is_null($getNpoptkp)) :
                throw new \Exception($this->outputMessage('not found', 'npotkp'));
            endif;

            /**
             * update data
             */
            $npoptkp = 'Tahun '.$request['tahun'];
            $updateNpoptkp = $this->npoptkp->where(['uuid_npoptkp' => $uuidNpoptkp])->update($request);

            if (!$updateNpoptkp) :
                throw new \Exception($this->outputMessage('update fail', $npoptkp));
            else :
                DB::commit();                
                $response = $this->success($this->outputMessage('updated', $npoptkp));
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
    public function delete($uuidNpoptkp)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->npoptkpChecker(["uuid_npoptkp" => $uuidNpoptkp]);
            $npoptkp  = is_null($getData) ? null : 'Npoptkp Tahun '.$getData->tahun;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'npotkp'));
            endif;

            /**
             * process delete
             */
            $delete = $this->npoptkp->where('uuid_npoptkp', $uuidNpoptkp)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', $npoptkp));
            else :
                throw new \Exception($this->outputMessage('undeleted', $npoptkp));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
