<?php

namespace App\Repositories\Bank\Briva;

/**
 * import component
 */

use Illuminate\Support\Carbon;

/**
 * import traits
 */

use App\Traits\Message;
use App\Traits\Response;
use App\Traits\Briva;

/**
 * import interface
 */

use App\Repositories\Bank\Briva\BrivaRepositories;

class EloquentBrivaRepositories implements BrivaRepositories
{
    use Message, Response, Briva;

    private $year;
    private $date;
    private $dateTime;
    private $timestamp;

    public function __construct()
    {
        /**
         * static value
         */
        $this->dateTime  = 'Y-m-d H:i:s';
        $this->date      = Carbon::now()->toDateString();
        $this->timestamp = gmdate("Y-m-d\TH:i:s.000\Z");
    }

    /**
     * create briva
     */
    public function create()
    {
        try {
            $brivaGenerateToken = $this->brivaGenerateToken();
            $expired = date($this->dateTime, strtotime('+60 days', strtotime($this->date)));
            $payload = [
                'custCode'        => "1255", // 4 digit tahun pajak + 9 digit no urut
                'nama'            => "John Doe", // nama wp
                'amount'          => "20000", // jumlah tagiham yg dibayar
                'keterangan'      => "PAJAK AIR TANAH", // jenis pajak
                'expiredDate'     => $expired
            ];
            $signature = $this->brivaGenareteSignature($brivaGenerateToken->access_token, $payload, $this->timestamp);
            $createBriva = $this->createBriva($brivaGenerateToken->access_token, $this->timestamp, $signature, $payload);
            $response = $createBriva;
        } catch (\Exception $e) {
            $response  = $this->error($e->getMessage());
        }
        return $response;
    }
}
