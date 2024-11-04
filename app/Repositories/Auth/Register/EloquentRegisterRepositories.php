<?php

namespace App\Repositories\Auth\Register;

/**
 * import component 
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Generator;

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

use App\Repositories\Auth\Register\RegisterRepositories;

class EloquentRegisterRepositories implements RegisterRepositories
{
    use Message, Response, Generator;

    private $logRepositories;

    private $user;
    private $checkerHelpers;

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
    }

    /**
     * store register notaris
     */
    public function storeNotaris($request)
    {
        DB::beginTransaction();
        try {

            /**
             * set form requuest
             */
            $request['password'] = Hash::make(trim($request['password']));
            $request['kode'] = $this->kodeNotaris();

            /**
             * save data
             */
            $saveData = $this->user->create($request);
            if (!$saveData) :
                throw new \Exception($this->outputMessage('unsaved', $request['name']));
            endif;

            DB::commit();
            $message = $this->outputMessage('saved', $request['name']) . ". Kode anda adalah " . $request['kode'] . ". Gunakan kode tersebut untuk login";
            $response  = $this->success($message);
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
