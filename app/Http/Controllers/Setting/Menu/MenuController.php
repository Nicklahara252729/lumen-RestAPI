<?php

namespace App\Http\Controllers\Setting\Menu;

/**
 * import collection 
 */

use App\Http\Controllers\Controller;

/**
 * import form request
 */

use Illuminate\Http\Request;
use App\Http\Requests\Setting\Menu\StoreRequest;
use App\Http\Requests\Setting\Menu\UpdateRequest;

/**
 * import traits
 */

use App\Traits\Message;

/**
 * import repositories 
 */

use App\Repositories\Setting\Menu\MenuRepositories;
use App\Repositories\Log\LogRepositories;

class MenuController extends Controller
{
    use Message;

    private $signature;
    private $logRepositories;
    private $menuRepositories;

    public function __construct(
        Request $request,
        MenuRepositories $menuRepositories,
        LogRepositories $logRepositories
    ) {
        /**
         * initialize repositories
         */
        $this->menuRepositories = $menuRepositories;
        $this->logRepositories = $logRepositories;

        /**
         * initialize component
         */
        $this->signature = base64_decode($request->header('signature'));
    }

    /**
     * get all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->menuRepositories->data();

        /**
         * save log
         */
        $log = $this->outputLogMessage('all data', 'menu');
        $this->logRepositories->saveLog($log['action'], $log['message'], $this->signature, null);

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * get data by uuid sub bidang
     */
    public function get($uuidMenu)
    {
        /**
         * process to database
         */
        $response = $this->menuRepositories->get($uuidMenu);

        /**
         * save log
         */
        $log = $this->outputLogMessage('single data', 'menu', 'uuid menu :' . $uuidMenu);
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
        $response = $this->menuRepositories->store($storeRequest);

        /**
         * save log
         */
        $log = $this->outputLogMessage('save', 'menu ' . $storeRequest['nama_menu']);
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
        $uuidMenu,
        UpdateRequest $updateRequest
    ) {
        /**
         * requesting data
         */
        $updateRequest = $updateRequest->all();

        /**
         * set log 
         */
        $log = $this->outputLogMessage('update', $uuidMenu, 'menu ' . json_encode($updateRequest), 'setting menu');

        /**
         * process begin
         */
        $response = $this->menuRepositories->update($uuidMenu, $updateRequest);

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
    public function delete($uuidMenu)
    {
        /**
         * set log
         */
        $log = $this->outputLogMessage('delete', $uuidMenu, null, 'setting menu');

        /**
         * process begin
         */
        $response = $this->menuRepositories->delete($uuidMenu);

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
