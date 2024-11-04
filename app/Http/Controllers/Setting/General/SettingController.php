<?php

namespace App\Http\Controllers\Setting\General;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Setting\General\UpdateRequest;

/**
 * import
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Setting\General\SettingRepositories;
use App\Repositories\Log\LogRepositories;

class SettingController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $settingRepositories;

    public function __construct(
        Request $request,
        SettingRepositories $settingRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->settingRepositories = $settingRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->settingRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'setting');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid bidang
     */
    public function get($param)
    {
        /**
         * process to database
         */
        $response = $this->settingRepositories->get($param);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'setting', 'setting');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * update data
     */
    public function update(
        UpdateRequest $updateRequest,
        $uuidSetting
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidSetting, json_encode($updateRequest), 'setting');

        /**
         * process begin
         */
        $response = $this->settingRepositories->update($updateRequest, $uuidSetting);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }
}
