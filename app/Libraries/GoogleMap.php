<?php

namespace App\Libraries;

use Exception;

class GoogleMap
{
    const GM_URL = 'https://maps.googleapis.com/maps/api/geocode';

    private $key;
    /**
     * @var string
     */
    private $latitude;

    /**
     * @var string
     */
    private $longitude;

    /**
     * GoogleMap constructor.
     *
     * @param string $latitude
     * @param string $longitude
     */
    public function __construct($latitude, $longitude, $key)
    {
        $this->key = $key;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Handle all process.
     *
     * @return array
     */
    public function handle()
    {
        $result = $this->push($this->getGmUrl());

        return $result;
    }

    /**
     * Get Google Vision API url.
     *
     * @return string
     */
    private function getGmUrl()
    {
        return sprintf("%s/json?latlng=%s,%s&key=%s", self::GM_URL, $this->latitude, $this->longitude, $this->key);
    }

    /**
     * Push the body (images and properties) to google vision.
     *
     * @param $gvUrl
     * @param $requestBody
     *
     * @return mixed|string
     * @throws \Exception
     */
    private function push($gmUrl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gmUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_errno($ch);
        curl_error($ch);

        curl_close($ch);

        $response = json_decode($response);
        
        if ($status != 200) {
            throw new \Exception(
                sprintf(
                    "Error: call to URL %s failed with status %s, response %s, curl_error: %s, curl_errno: %d",
                    self::GM_URL,
                    $status,
                    $response,
                    curl_error($ch),
                    curl_errno($ch)
                )
            );
        }        

        return $response;
    }
}
