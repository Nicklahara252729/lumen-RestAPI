<?php

namespace App\Traits;

trait Briva
{
    /**
     * config value
     */
    private function configBriva()
    {
        $set = [
            'accessTokenUrl' => env('BRIVA_ACCESS_TOKEN_URL'),
            'apiUrl' => env('BRIVA_API_URL'),
            'clientId' => env('BRIVA_CLIENT_ID'),
            'clientSecret' => env('BRIVA_CLIENT_SECRET'),
            'path' => env('BRIVA_PATH'),
            'brivaNo' => env('BRIVA_NO'),
            'institutionCode' => env('BRIVA_INSTITUTION_CODE'),
        ];
        return $set;
    }

    /**
     * BRIVA generate token
     */
    public function brivaGenerateToken()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $this->configBriva()['apiUrl'] . $this->configBriva()['accessTokenUrl'],
            CURLOPT_POST => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => 'client_id=' . $this->configBriva()['clientId'] . '&client_secret=' . $this->configBriva()['clientSecret']
        ));

        curl_close($curl);
        $response = json_decode(curl_exec($curl));
        return $response;
    }

    /**
     * BRIVA generate signature
     */
    public function brivaGenareteSignature($token, $payload, $timestamp)
    {
        $payload      = json_encode(array_merge(
            [
                'institutionCode' => $this->configBriva()['institutionCode'],
                'brivaNo'         => $this->configBriva()['brivaNo']
            ],
            $payload
        ));
        $path         = $this->configBriva()['path'];
        $verb         = 'POST';
        $secret       = $this->configBriva()['clientSecret'];
        $payloads     = "path=$path&verb=$verb&token=Bearer $token&timestamp=$timestamp&body=$payload";
        $signPayload  = hash_hmac('sha256', $payloads, $secret, true);
        return base64_encode($signPayload);
    }

    /**
     * create briva
     */
    public function createBriva($token, $timestamp, $signature, $payload)
    {
        $request_headers = array(
            "Content-Type:" . "application/json",
            "Authorization: Bearer " . $token,
            "BRI-Timestamp:" . $timestamp,
            "BRI-Signature:" . $signature,
        );
        $payload = json_encode(array_merge(
            [
                'institutionCode' => $this->configBriva()['institutionCode'],
                'brivaNo'         => $this->configBriva()['brivaNo']
            ],
            $payload
        ));
        $urlPost = $this->configBriva()['apiUrl'] . $this->configBriva()['path'];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $urlPost,
            CURLOPT_HTTPHEADER => $request_headers,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_RETURNTRANSFER => true,
        ));

        $resultPost = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($resultPost, true);
        return $response;
    }

    /**
     * get briva
     */
    public function getBriva($token, $timestamp, $signature, $custCode)
    {
        $request_headers = array(
            "Content-Type:" . "application/json",
            "Authorization: Bearer " . $token,
            "BRI-Timestamp:" . $timestamp,
            "BRI-Signature:" . $signature,
        );
        $urlGet = $this->configBriva()['apiUrl'] . $this->configBriva()['path'] . '/' . $this->configBriva()['institutionCode'] . '/' . $this->configBriva()['brivaNo'] . '/' . $custCode;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $urlGet,
            CURLOPT_HTTPHEADER => $request_headers,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLINFO_HEADER_OUT => true,
            CURLOPT_RETURNTRANSFER => true,
        ));

        $resultPost = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($resultPost, true);
        return $response;
    }

    /**
     * kode briva
     */
    public function kodeBriva($custCode)
    {
        return $this->configBriva()['brivaNo'] . $custCode;
    }
}
