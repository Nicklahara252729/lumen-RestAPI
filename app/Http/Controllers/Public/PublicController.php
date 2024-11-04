<?php

namespace App\Http\Controllers\Public;

/**
 * import component
 */

use App\Http\Controllers\Controller;

/**
 * import
 */

use App\Traits\Message;

/**
 * import repositories
 */

use App\Repositories\Setting\Slider\SliderRepositories;
use App\Repositories\Public\PublicRepositories;

class PublicController extends Controller
{
    use Message;

    private $signature;
    private $sliderRepositories;
    private $publicRepositories;

    public function __construct(
        PublicRepositories $publicRepositories,
        SliderRepositories $sliderRepositories,
    ) {
        /**
         * initialize repositories
         */
        $this->sliderRepositories = $sliderRepositories;
        $this->publicRepositories = $publicRepositories;
    }

    /**
     * data slider
     */
    public function slider()
    {
        /**
         * load data from repositories
         */
        $response = $this->sliderRepositories->data();

        /**
         * response
         */
        return response()->json($response);
    }

    /**
     * data realisasi
     */
    public function realisasi()
    {
        /**
         * load data from repositories
         */
        $response = $this->publicRepositories->realisasi();

        /**
         * response
         */
        return response()->json($response);
    }
}
