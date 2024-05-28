<?php

namespace App\Http;

class Curl
{
    public static function file_get_contents_by_curl(string $url) : string|bool
    {
        $curl_p = curl_init();
        curl_setopt($curl_p, CURLOPT_URL, $url);
        curl_setopt($curl_p, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_p, CURLOPT_ENCODING, "gzip");
        $data = curl_exec($curl_p);
        curl_close($curl_p);
        return $data;
    }
}
