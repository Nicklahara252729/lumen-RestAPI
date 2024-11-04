<?php

/**
 * file location
 */

namespace App\Http\Controllers\Refrensi\Provinsi;

/**
 * import component
 */

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * import repositories
 */

use App\Repositories\Refrensi\Provinsi\ProvinsiRepositories;

class ProvinsiController extends Controller
{

    private $request;
    private $provinsiRepositories;

    public function __construct(
        Request $request,
        ProvinsiRepositories $provinsiRepositories
    ) {
        /**
         * define component
         */
        $this->request = $request;

        /**
         * initialize repositories
         */
        $this->provinsiRepositories = $provinsiRepositories;
    }

    /**
     * all record data
     */
    public function data()
    {
        /**
         * load data from repositories
         */
        $response = $this->provinsiRepositories->data();

        /**
         * response
         */
        return response()->json($response);
    }
}
