<?php

namespace App\Repositories\Setting\Slider;

/**
 * import component
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

use App\Models\Setting\Slider\Slider;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import repositories
 */

use App\Repositories\Setting\Slider\SliderRepositories;

class EloquentSliderRepositories implements SliderRepositories
{
    use Message, Response;

    private $slider;
    private $checkerHelpers;
    private $storage;

    public function __construct(
        Slider $slider,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->slider = $slider;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->storage = path('slider');
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
            $data = $this->slider->orderBy('id','asc')->get();

            $output = [];
            foreach ($data as $key => $value) :
                $set = [
                    'uuid_slider' => $value->uuid_slider,
                    'slider_name' => url($this->storage . $value->slider_name),
                    'title' => $value->title,
                    'description' => $value->description,
                ];
                array_push($output, $set);
            endforeach;
            $response  = $this->successData($this->outputMessage('data', count($data)), $output);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid
     */
    public function get($uuidSlider)
    {
        try {
            $getSlider = $this->checkerHelpers->sliderChecker(["uuid_slider" => $uuidSlider]);
            if (is_null($getSlider)) :
                throw new \Exception($this->outputMessage('not found', 'slider'));
            endif;
            $response  = $this->successData($this->outputMessage('data', 1), $getSlider);
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
             * move file data 
             */
            $fileName        = $_FILES['slider_name']['name'];
            $fileTempName    = $_FILES['slider_name']['tmp_name'];
            $fileExt         = explode('.', $fileName);
            $fileActualExt   = strtolower(end($fileExt));
            $fileNameNew     = Uuid::uuid4()->getHex() . "." . $fileActualExt;
            $fileDestination = $this->storage . '/' . $fileNameNew;
            $request['slider_name'] = $fileNameNew;

            /**
             * save data bidang
             */
            $saveData = $this->slider->create($request);
            if ($saveData && move_uploaded_file($fileTempName, $fileDestination)) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', $request['slider_name']));
            else :
                throw new \Exception($this->outputMessage('unsaved', $request['slider_name']));
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
    public function update($request, $uuidSlider)
    {
        DB::beginTransaction();
        try {

            /**
             * check if data exist
             */
            $getSlider     = $this->checkerHelpers->sliderChecker(["uuid_slider" => $uuidSlider]);
            $sliderDataOld = isset($getSlider->slider_name) ? $getSlider->slider_name : null;
            if (is_null($getSlider)) :
                throw new \Exception($this->outputMessage('not found', 'slider'));
            endif;

            /**
             * if file exist
             */
            if (isset($_FILES['slider_name'])) :

                /**
                 * remove file
                 */
                if (file_exists($this->storage . $sliderDataOld)) :
                    unlink($this->storage . $sliderDataOld);
                endif;

                /**
                 * upload file
                 */
                $fileName        = $_FILES['slider_name']['name'];
                $fileTempName    = $_FILES['slider_name']['tmp_name'];
                $fileExt         = explode('.', $fileName);
                $fileActualExt   = strtolower(end($fileExt));
                $fileNemeNew     = Uuid::uuid4()->getHex() . "." . $fileActualExt;
                $fileDestination = $this->storage . '/' . $fileNemeNew;
                move_uploaded_file($fileTempName, $fileDestination);
                $request['slider_name'] = $fileNemeNew;
            endif;

            $sliderFile = isset($_FILES['slider_name']) ? $request['slider_name'] : $sliderDataOld;

            /**
             * update data
             */
            $updateData = $this->slider->where(['uuid_slider' => $uuidSlider])->update($request);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('update fail', $sliderFile));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', $sliderFile));
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
    public function delete($uuidSlider)
    {
        DB::beginTransaction();
        try {
            /**
             * is data valid
             */
            $getData    = $this->checkerHelpers->sliderChecker(["uuid_slider" => $uuidSlider]);
            $sliderFile = isset($getData->slider_name) ? $getData->slider_name : null;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'slider'));
            endif;

            /**
             * remove files
             */            
            if (!is_null($sliderFile)) :
                if (file_exists($this->storage . $sliderFile)) :
                    if (!unlink($this->storage . $sliderFile)) :
                        throw new \Exception($this->outputMessage('remove fail', $sliderFile));
                    endif;
                endif;
            endif;

            /**
             * delete data
             */
            $delete = $this->slider->where('uuid_slider', $uuidSlider)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', $sliderFile));
            else :
                throw new \Exception($this->outputMessage('undeleted', $sliderFile));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
