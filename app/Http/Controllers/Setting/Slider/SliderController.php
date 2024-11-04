<?php

namespace App\Http\Controllers\Setting\Slider;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Setting\Slider\StoreRequest;
use App\Http\Requests\Setting\Slider\UpdateRequest;

/**
 * import
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Setting\Slider\SliderRepositories;
use App\Repositories\Log\LogRepositories;

class SliderController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $sliderRepositories;

    public function __construct(
        Request $request,
        SliderRepositories $sliderRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize middleware
         */
        // $this->middleware('auth:api', ['except' => ['data']]);
        // $this->middleware('signature', ['except' => ['data']]);

        /**
         * initialize repositories
         */
        $this->sliderRepositories = $sliderRepositories;
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
        $response = $this->sliderRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'slider');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid slider
     */
    public function get($uuidSlider)
    {
        /**
         * process to database
         */
        $response = $this->sliderRepositories->get($uuidSlider);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'slider', 'uuid slider :' . $uuidSlider);
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * store data
     */
    public function store(
        StoreRequest $storeRequest
    ) {
        /**
         * requesting data
         */
        $storeRequest = $storeRequest->all();

        /**
         * process begin
         */
        $response = $this->sliderRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'slider ' . $storeRequest['title']);
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
        $uuidSlider,
        UpdateRequest $updateRequest
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidSlider, 'slider ' . json_encode($updateRequest), 'setting slider');

        /**
         * process begin
         */
        $response = $this->sliderRepositories->update($updateRequest, $uuidSlider);

        /**
         * save log
         */
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * delete data
     */
    public function delete($uuidSlider)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidSlider, null, 'setting slider');

        /**
         * process begin
         */
        $response = $this->sliderRepositories->delete($uuidSlider);

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
