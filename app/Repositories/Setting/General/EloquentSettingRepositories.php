<?php

namespace App\Repositories\Setting\General;

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

use App\Models\Setting\Setting\Setting;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Setting\General\SettingRepositories;

class EloquentSettingRepositories implements SettingRepositories
{
    use Message, Response;

    private $setting;
    private $checkerHelpers;

    public function __construct(
        Setting $setting,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->setting = $setting;

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
             * data setting
             */
            $data = $this->setting->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get data by uuid
     */
    public function get($param)
    {
        try {
            /**
             * helpers
             */

            $getSetting = $this->checkerHelpers->settingChecker($param);
            if (is_null($getSetting)) :
                throw new \Exception($this->outputMessage('not found', 'setting'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getSetting);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update data to db
     */
    public function update($request, $uuidSetting)
    {
        DB::beginTransaction();
        try {
            /**
             * helpers
             */
            $getSetting = $this->checkerHelpers->settingChecker($uuidSetting);
            if (is_null($getSetting)) :
                throw new \Exception($this->outputMessage('not found', 'setting'));
            endif;

            $updateSetting = $this->setting->where(['uuid_setting' => $uuidSetting])->update($request);

            if (!$updateSetting) :
                throw new \Exception($this->outputMessage('update fail', $request['category']));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', $request['category']));
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
}
