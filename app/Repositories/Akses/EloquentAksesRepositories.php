<?php

namespace App\Repositories\Akses;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\Akses\Akses;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Akses\AksesRepositories;

class EloquentAksesRepositories implements AksesRepositories
{
    use Message, Response;

    private $akses;
    private $checkerHelpers;
    private $date;

    public function __construct(
        Akses $akses,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->akses = $akses;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;

        /**
         * static value
         */
        $this->date    = Carbon::now()->toDateTimeString();
    }

    /**
     * all record
     */
    public function data()
    {
        try {
            /**
             * data akses
             */
            $data = $this->akses->select("nama_menu", "nama_bidang", "role", "uuid_akses", "akses.uuid_menu")
                ->join("menus", "akses.uuid_menu", "=", "menus.uuid_menu")
                ->join("bidang", "akses.uuid_bidang", "=", "bidang.uuid_bidang")
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
     * get single data
     */
    public function get($uuidAkses)
    {
        try {
            /**
             * helpers
             */

            $getAkses = $this->checkerHelpers->aksesChecker(["uuid_akses" => $uuidAkses]);
            if (is_null($getAkses)) :
                throw new \Exception($this->outputMessage('not found', 'akses'));
            endif;

            $response  = $this->successData($this->outputMessage('data', 1), $getAkses);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * get by role bidang
     */
    public function getByRoleBidang($role, $uuidBidang)
    {
        try {
            /**
             * helpers
             */

            $getAkses = $this->akses->where(["role" => $role, 'uuid_bidang' => $uuidBidang])->get();
            $response  = $this->successData($this->outputMessage('data', count($getAkses)), $getAkses);
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
             * filter data to insert or delete
             */
            $toDelete = [];
            $toInsert = [];
            foreach ($request['role'] as $key => $value) {
                if (
                    empty($request['uuid_akses'][$key]) ||
                    is_null($request['uuid_akses'][$key]) ||
                    !isset($request['uuid_akses'][$key]) ||
                    $request['uuid_akses'][$key] == ""
                ) :
                    $setToInsert = [
                        'uuid_akses' => (string) Str::orderedUuid(),
                        'role'       => $value,
                        'uuid_bidang' => $request['uuid_bidang'][$key],
                        'uuid_menu'  => $request['uuid_menu'][$key],
                        'created_at' => $this->date,
                        'updated_at' => $this->date
                    ];
                    array_push($toInsert, $setToInsert);
                else :
                    $setToDelete = [$request['uuid_akses'][$key]];
                    array_push($toDelete, $setToDelete);
                endif;
            }

            /**
             * insert process
             */
            $process   = $this->akses->insert($toInsert);
            if ($process) :

                /**
                 * delete process
                 */
                if (sizeof($toDelete) > 0) :
                    $process = $this->akses->whereIn('uuid_akses', $toDelete)->delete();
                endif;
            endif;

            if ($process) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', 'akses'));
            else :
                throw new \Exception($this->outputMessage('unsaved', 'akses'));
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
