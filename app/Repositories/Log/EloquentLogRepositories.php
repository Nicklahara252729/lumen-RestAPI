<?php

namespace App\Repositories\Log;

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

use App\Models\Log\LogUser\LogUser;
use App\Models\Log\LogBphtb\LogBphtb;

/**
 * import interface
 */

use App\Repositories\Log\LogRepositories;

class EloquentLogRepositories implements LogRepositories
{
    use Message, Response;

    private $logUser;
    private $logBphtb;

    public function __construct(
        LogUser $logUser,
        LogBphtb $logBphtb
    ) {
        /**
         * initialize model
         */
        $this->logUser = $logUser;
        $this->logBphtb = $logBphtb;
    }

    /**
     * store data to db
     */
    public function saveLog($action, $keterangan, $uuidUser, $nop)
    {
        DB::beginTransaction();
        try {
            /**
             * save data log
             */
            $data = [
                'nop' => $nop,
                'action' => $action,
                'keterangan' => $keterangan,
                'uuid_user' => $uuidUser,
            ];
            $save = $this->logUser->create($data);
            if ($save) :
                DB::commit();
                $response  = $this->success($this->outputMessage('saved', $action));
            else :
                throw new \Exception($this->outputMessage('unsaved', $action));
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
     * store data bphtb to db
     */
    public function saveLogBphtb($action, $keterangan, $uuidUser, $noRegistrasi)
    {
        DB::beginTransaction();
        try {
            /**
             * save data log
             */
            $data = [
                'no_registrasi' => $noRegistrasi,
                'action' => $action,
                'keterangan' => $keterangan,
                'uuid_user' => $uuidUser,
            ];
            $save = $this->logBphtb->create($data);
            if (!$save) :
                throw new \Exception($this->outputMessage('unsaved', $action));
            endif;

            DB::commit();
            $response  = $this->success($this->outputMessage('saved', $action));
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
     * all record
     */
    public function logBphtb($noRegistrasi)
    {
        try {
            /**
             * data log
             */
            $data = $this->logBphtb->select('no_registrasi', 'action', 'keterangan')
                ->selectRaw('(SELECT name FROM users where uuid_user = log_bphtb.uuid_user) AS user')
                ->selectRaw("DATE_FORMAT(created_at, '%d %M %Y ; %H:%i:%s') AS tanggal")
                ->where('no_registrasi',$noRegistrasi)
                ->get();

            /**
             * set response
             */
            $response = $this->successData($this->outputMessage('data', count($data)), $data);
        } catch (\Exception $e) {
            $response = $this->error($e->getMessage());
        }
        return $response;
    }
}
