<?php

namespace App\Repositories\Setting\Menu;

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

use App\Models\Setting\Menu\Menu;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Setting\Menu\MenuRepositories;

class EloquentMenuRepositories implements MenuRepositories
{
    use Message, Response;

    private $menu;
    private $checkerHelpers;

    public function __construct(
        Menu $menu,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->menu = $menu;

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
             * data menu
             */
            $data   = $this->menu->get();

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
     * get single data
     */
    public function get($uuidMenu)
    {
        try {
            /**
             * helpers
             */

            $getMenu = $this->checkerHelpers->menuChecker(["uuid_menu" => $uuidMenu]);
            if (is_null($getMenu)) :
                throw new \Exception($this->outputMessage('not found', 'menu'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getMenu);
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
             * save data menu
             */
            $saveData = $this->menu->create($request);
            if ($saveData) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', $request['nama_menu']));
            else :
                throw new \Exception($this->outputMessage('unsaved', $request['nama_menu']));
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
    public function update($uuidMenu, $request)
    {
        DB::beginTransaction();
        try {
            /**
             * check if data exist
             */
            $getMenu = $this->checkerHelpers->menuChecker(["uuid_menu" => $uuidMenu]);
            if (is_null($getMenu)) :
                throw new \Exception($this->outputMessage('not found', 'menu'));
            endif;

            /**
             * update data
             */
            $updateMenu = $this->menu->where(['uuid_menu' => $uuidMenu])->update($request);
            if (!$updateMenu) :
                throw new \Exception($this->outputMessage('update fail', $request['nama_menu']));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', $request['nama_menu']));
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
    public function delete($uuidMenu)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData = $this->checkerHelpers->menuChecker(["uuid_menu" => $uuidMenu]);
            $menu  = is_null($getData) ? null : $getData->nama_menu;
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'menu'));            
            endif;

            /**
             * proses delete
             */
            $delete = $this->menu->where('uuid_menu', $uuidMenu)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', $menu));
            else :
                throw new \Exception($this->outputMessage('undeleted', $menu));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
