<?php

namespace App\Traits;

/**
 * import traits
 */

use App\Traits\Cipher;

/**
 * import helper
 */

use App\Libraries\CheckerHelpers;

trait Notification
{
    use Cipher;

    /**
     * whatsapp
     */
    private function whatsapp($target, $message)
    {
        $checkerHelper = new CheckerHelpers;
        $getKey = $checkerHelper->settingChecker('whatsapp key');
        // $getKey = $this->encipher($getKey->description, env('CIPHER_KEY'));
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $getKey->description
            ),
        ));

        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response;
    }
}
