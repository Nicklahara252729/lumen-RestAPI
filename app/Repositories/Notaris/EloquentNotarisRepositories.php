<?php

namespace App\Repositories\Notaris;


/**
 * import collection
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;

/**
 * import models
 */

use App\Models\User\User;

/**
 * import helpers
 */

use App\Libraries\CheckerHelpers;

/**
 * import interface
 */

use App\Repositories\Notaris\NotarisRepositories;

class EloquentNotarisRepositories implements NotarisRepositories
{
    use Message, Response;

    private $user;
    private $checkerHelpers;
    private $datetime;
    private $storage;

    public function __construct(
        User $user,
        CheckerHelpers $checkerHelpers,
    ) {
        /**
         * initialize model
         */
        $this->user = $user;

        /**
         * initialize helper
         */
        $this->checkerHelpers = $checkerHelpers;
        $this->datetime = Carbon::now()->toDateTimeLocalString();

        /**
         * static value
         */
        $this->storage = path('user');
    }

    /**
     * query data
     */
    public function queryData()
    {
        $data = $this->user->select(
            'uuid_user',
            'name',
            'email',
            'username',
            'no_hp',
            DB::raw('CONCAT("' . url($this->storage) . '/", CASE WHEN profile_photo_path IS NULL THEN "blank.png" ELSE  profile_photo_path END) AS profile_photo_path'),
            'kode',
            'alamat',
            'kontak_person',
            'kota',
            'is_verified'
        )
            ->where('role', 'notaris')
            ->whereNull('deleted_at');
        return $data;
    }

    /**
     * all record data
     */
    public function data()
    {
        try {
            $data = $this->queryData()->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * status verifikasi
     */
    public function verifikasi($uuidUser)
    {
        DB::beginTransaction();
        try {

            /**
             * check if data exist
             */
            $getData = $this->checkerHelpers->userChecker(["uuid_user" => $uuidUser]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'notaris'));
            endif;

            /**
             * update data
             */
            $update = $this->user->where(['uuid_user' => $uuidUser])->update(['is_verified' => 1]);
            if (!$update) :
                throw new \Exception($this->outputMessage('update fail', 'status verifikasi'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'status verifikasi'));
        } catch (\Exception $e) {
            DB::rollback();
            $response = $this->error($e->getMessage());
        }
        /**
         * send response to controller
         */
        return $response;
    }

    /**
     * get data by uuid
     */
    public function get($uuidUser)
    {
        try {
            /**
             * helpers
             */

            $getUser = $this->user->select('uuid_user', 'name', 'alamat', 'kota', 'no_hp', 'kontak_person')
                ->where(['uuid_user' => $uuidUser])
                ->firsT();
            if (is_null($getUser)) :
                throw new \Exception($this->outputMessage('not found', 'user'));
            else :
                $response  = $this->successData($this->outputMessage('data', 1), $getUser);
            endif;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update data to db
     */
    public function update($uuidUser, $request)
    {
        DB::beginTransaction();
        try {

            /**
             * get user
             */
            $getUser  = $this->checkerHelpers->userChecker(["uuid_user" => $uuidUser]);
            if (is_null($getUser)) :
                throw new \Exception($this->outputMessage('not found', 'user'));
            endif;

            /**
             * update data
             */
            $updateData = $this->user->where(['uuid_user' => $uuidUser])->update($request);
            if (!$updateData) :
                throw new \Exception($this->outputMessage('update fail', $request['name']));
            else :
                DB::commit();
                $response = $this->success($this->outputMessage('updated', $request['name']));
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
    public function delete($uuidUser)
    {
        DB::beginTransaction();
        try {
            /**
             * check data
             */
            $getData   = $this->checkerHelpers->userChecker(["uuid_user" => $uuidUser]);
            if (is_null($getData)) :
                throw new \Exception($this->outputMessage('not found', 'pengguna'));
            endif;
            $name = $getData->name;

            /**
             * delete data
             */
            $delete = $this->user->where('uuid_user', $uuidUser)->update(['deleted_at' => $this->datetime]);
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', $name));
            else :
                throw new \Exception($this->outputMessage('undeleted', $name));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * search data
     */
    public function search($request)
    {
        try {
            $data = $this->queryData()->where('name', 'like', '%' . $request->name . '%')->get();
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
