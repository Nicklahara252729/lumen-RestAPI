<?php

namespace App\Repositories\User;

/**
 * default component
 */

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;

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
 * import repositories
 */

use App\Repositories\User\UserRepositories;

class EloquentUserRepositories implements UserRepositories
{
    use Message, Response;

    private $user;
    private $checkerHelpers;
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

        /**
         * static value
         */
        $this->storage = path('user');
    }

    /**
     * query data
     */
    public function queryData($param = null)
    {
        $data = $this->user->leftJoin("bidang", "bidang.uuid_bidang", "=", "users.uuid_bidang")
            ->leftJoin("sub_bidang", "sub_bidang.uuid_sub_bidang", "=", "users.uuid_sub_bidang")
            ->leftJoin("ref_kecamatan", "ref_kecamatan.KD_KECAMATAN", "=", "users.kd_kecamatan")
            ->leftJoin('ref_kelurahan', function ($join) {
                $join->on('users.kd_kecamatan', '=', 'ref_kelurahan.KD_KECAMATAN')
                    ->on('users.kd_kelurahan', '=', 'ref_kelurahan.KD_KELURAHAN');
            })
            ->select(
                "users.*",
                "nama_bidang",
                "nama_sub_bidang",
                DB::raw('CASE WHEN profile_photo_path IS NULL THEN NULL
                    ELSE CONCAT("' . url($this->storage) . '/", profile_photo_path) END AS profile_photo_path'),
                DB::raw('IFNULL(ref_kecamatan.NM_KECAMATAN, NULL) AS nm_kecamatan'),
                DB::raw('IFNULL(ref_kelurahan.NM_KELURAHAN, NULL) AS nm_kelurahan'),
            );
        return $data;
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
            $data = $this->queryData()->get();

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
    public function get($param)
    {
        try {
            /**
             * helpers
             */

            $getUser = $this->checkerHelpers->userJoinBidangSubBidangChecker($param);
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
     * store data to db
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $request['password'] = Hash::make(trim($request['password']));

            /**
             * move file data
             */
            if (isset($_FILES['profile_photo_path'])) :

                $photoName        = $_FILES['profile_photo_path']['name'];
                $photoTempName    = $_FILES['profile_photo_path']['tmp_name'];
                $photoExt         = explode('.', $photoName);
                $photoActualExt   = strtolower(end($photoExt));
                $photoNew         = Uuid::uuid4()->getHex() . "." . $photoActualExt;
                $photoDestination = $this->storage . '/' . $photoNew;

                if (!move_uploaded_file($photoTempName, $photoDestination)) :
                    throw new \Exception($this->outputMessage('directory'));
                endif;

                $request['profile_photo_path'] = $photoNew;
            endif;

            /**
             * save data
             */
            $saveData = $this->user->create($request);
            if ($saveData) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', $request['name']));
            else :
                throw new \Exception($this->outputMessage('unsaved', $request['name']));
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
    public function update($uuidUser, $request)
    {
        DB::beginTransaction();
        try {

            /**
             * get user
             */
            $getUser  = $this->checkerHelpers->userChecker(["uuid_user" => $uuidUser]);
            $photoOld = !is_null($getUser->profile_photo_path) ? $getUser->profile_photo_path : null;
            if (is_null($getUser)) :
                throw new \Exception($this->outputMessage('not found', 'user'));
            endif;

            /**
             * if file exist
             */
            if (isset($_FILES['profile_photo_path'])) :
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
                $photoName        = $_FILES['profile_photo_path']['name'];
                $photoTempName    = $_FILES['profile_photo_path']['tmp_name'];
                $photoExt         = explode('.', $photoName);
                $photoActualExt   = strtolower(end($photoExt));
                $photoNew         = Uuid::uuid4()->getHex() . "." . $photoActualExt;
                $photoDestination = $this->storage . '/' . $photoNew;
                move_uploaded_file($photoTempName, $photoDestination);
                $request['profile_photo_path'] = $photoNew;
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
            $photoFile = $getData->profile_photo_path;

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
            $delete = $this->user->where('uuid_user', $uuidUser)->delete();
            if ($delete) :
                DB::commit();
                $response = $this->success($this->outputMessage('deleted', 'pengguna'));
            else :
                throw new \Exception($this->outputMessage('undeleted', 'pengguna'));
            endif;
        } catch (\Exception $e) {
            DB::rollback();
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }

    /**
     * update password
     */
    public function updatePassword($uuidUser, $request)
    {
        DB::beginTransaction();
        try {
            /**
             * form setting
             */
            $request = collect($request)->except(['current_password', 'password_confirmation', 'new_password_confirmation'])->toArray();
            $input['password'] = isset($request['password']) ? Hash::make($request['password']) : Hash::make($request['new_password']);

            /**
             * update data outlet
             */

            $updatePassword = $this->user->where(["uuid_user" => $uuidUser])->update($input);
            if (!$updatePassword) :
                throw new \Exception($this->outputMessage('update fail', 'password'));
            endif;

            DB::commit();
            $response = $this->success($this->outputMessage('updated', 'password'));
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
            /**
             * data bidang
             */
            $data = $this->queryData()->where('name', 'like', '%' . $request->name . '%')->get();

            /**
             * set response
             */
            $response  = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
