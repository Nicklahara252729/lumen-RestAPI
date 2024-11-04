<?php

namespace App\Repositories\Layanan\Layanan;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Layanan\Layanan\Layanan;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Layanan\Layanan\LayananRepositories;

class EloquentLayananRepositories implements LayananRepositories
{
    use Message, Response;

    private $layanan;
    private $checkerHelpers;
    private $storage;

    public function __construct(
        Layanan $layanan,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->layanan = $layanan;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->storage = path('layanan');
    }

    /**
     * all record
     */
    public function data()
    {
        try {
            /**
             * data layanan
             */
            $data = $this->layanan->select(DB::raw("uuid_layanan, layanan, status, CONCAT('" . url($this->storage) . "/', icon) AS icon"))->get();

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
     * get data by uuid
     */
    public function get($uuidLayanan)
    {
        try {
            /**
             * helpers
             */

            $getLayanan = $this->checkerHelpers->layananChecker(["uuid_layanan" => $uuidLayanan]);
            if (is_null($getLayanan)) :
                throw new \Exception($this->outputMessage('not found', 'layanan'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getLayanan);
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
             * icon file
             */
            if (isset($_FILES['icon'])) :
                $photoName        = $_FILES['icon']['name'];
                $photoTempName    = $_FILES['icon']['tmp_name'];
                $photoExt         = explode('.', $photoName);
                $photoActualExt   = strtolower(end($photoExt));
                $photoNew         = Uuid::uuid4()->getHex() . "." . $photoActualExt;
                $photoDestination = $this->storage . '/' . $photoNew;

                if (!move_uploaded_file($photoTempName, $photoDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;

                $request['icon'] = $photoNew;
            endif;

            /**
             * save data layanan
             */
            $saveData = $this->layanan->create($request);
            if ($saveData) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', $request['layanan']));
            else :
                throw new \Exception($this->outputMessage('unsaved', $request['layanan']));
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
    public function update($request, $uuidLayanan)
    {
        DB::beginTransaction();
        try {

            /**
             * get layanan
             */
            $getLayanan = $this->checkerHelpers->layananChecker(["uuid_layanan" => $uuidLayanan]);
            $photoOld = !is_null($getLayanan->icon) ? $getLayanan->icon : null;
            if (is_null($getLayanan)) :
                throw new \Exception($this->outputMessage('not found', 'layanan'));
            endif;

            /**
             * if file exist
             */
            if (isset($_FILES['icon'])) :
                /**
                 * remove file
                 */
                if (!is_null($photoOld)) :
                    if (file_exists($this->storage . "/" . $photoOld)) :
                        if (!unlink($this->storage . "/" . $photoOld)) :
                            throw new \Exception($this->outputMessage('remove fail', $photoOld));
                        endif;
                    endif;
                endif;

                /**
                 * upload file
                 */
                $photoName        = $_FILES['icon']['name'];
                $photoTempName    = $_FILES['icon']['tmp_name'];
                $photoExt         = explode('.', $photoName);
                $photoActualExt   = strtolower(end($photoExt));
                $photoNew         = Uuid::uuid4()->getHex() . "." . $photoActualExt;
                $photoDestination = $this->storage . '/' . $photoNew;
                move_uploaded_file($photoTempName, $photoDestination);
                $request['icon'] = $photoNew;
            endif;


            /**
             * update data
             */
            $updateLayanan = $this->layanan->where(['uuid_layanan' => $uuidLayanan])->update($request);
            if (!$updateLayanan) :
                throw new \Exception($this->outputMessage('update fail', $request['layanan']));
            else :

                DB::commit();
                $response = $this->success($this->outputMessage('updated', $request['layanan']));
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
    public function delete($uuidLayanan)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->layananChecker(["uuid_layanan" => $uuidLayanan]);
            $layanan  = is_null($getData) ? null : $getData->layanan;
            $photoFile = $getData->icon;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'layanan'));
            endif;

            /**
             * remove foto
             */
            if (!is_null($photoFile)) :
                if (file_exists($this->storage . "/" . $photoFile)) :
                    if (!unlink($this->storage . "/" . $photoFile)) :
                        throw new \Exception($this->outputMessage('remove fail', $photoFile));
                    endif;
                endif;
            endif;


            /**
             * delete data
             */
            $delete = $this->layanan->where('uuid_layanan', $uuidLayanan)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', $layanan));
            else :
                throw new \Exception($this->outputMessage('undeleted', $layanan));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
