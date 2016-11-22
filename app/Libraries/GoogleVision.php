<?php

namespace App\Libraries;

use Exception;

class GoogleVision
{
    const GV_URL = 'https://vision.googleapis.com/v1/images:annotate';

    private $key;

    private $images = [];

    private $type;

    public function __construct($key, $type = 'TEXT_DETECTION')
    {
        $this->config = $key;
        $this->type = $type;
    }

    /**
     * Handle all process.
     *
     * @return array
     */
    public function handle()
    {
        $result = [];
        foreach ($this->getImages() as $image) {
            $body = $this->buildRequestBody($image);
            dd($body);
            $result[] = $this->push($this->getGvUrl(), $body);
        }

        return $result;
    }

    /**
     * Set the images that we want to push.
     *
     * @param array $images
     *
     * @return $this
     */
    public function setImages(array $images)
    {
        if (empty($array)) {
            throw new Exception('There is no images in parameter arguments setImage(array $images)');
        }
        $this->images = $images;

        return $this;
    }

    /**
     * Get all images
     *
     * @return array
     * @throws \Exception
     */
    public function getImages()
    {
        if (empty($this->images)) {
            throw new \Exception('There is no images will be process!');
        }

        return $this->images;
    }

    /**
     * Get Google Vision API url.
     *
     * @return string
     */
    private function getGvUrl()
    {
        return sprintf("%s?key=%s", self::GV_URL, $this->key);
    }

    /**
     * Build the request body.
     *
     * @param $image
     *
     * @return string
     */
    protected function buildRequestBody($image)
    {
        return json_encode([
            'requests' => [
                'image' => [
                    'content' => base64_encode($image),
                ],
                'features' => [
                    'type' => $this->type,
                    'maxResults' => 500,
                ],
            ],
        ]);
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
    private function push($gvUrl, $requestBody)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $gvUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
        $jsonResponse = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_errno($curl);
        curl_error($curl);

        curl_close($curl);
        if ($status != 200) {
            throw new \Exception(
                sprintf(
                    "Error: call to URL %s failed with status %s, response %s, curl_error: %s, curl_errno: %d",
                    self::GV_URL,
                    $status,
                    $jsonResponse,
                    curl_error($curl),
                    curl_errno($curl)
                )
            );
        }

        return $jsonResponse;
    }
}
