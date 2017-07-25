<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class LoyaltyController extends BaseApiController
{

    const CACHE_EXPIRED_TOKEN = 'expired.token.egift';

    public function index()
    {
        try {

            if (cache(self::CACHE_EXPIRED_TOKEN)) {
                $accessToken = cache(self::CACHE_EXPIRED_TOKEN);
            } else {
                $accessToken = $this->getAccessToken();
            }

            //request list egift
            $method = 'POST';
            $url = config('common.egift.url_list');
            $key = 'Bearer '.$accessToken;
            $data = [];

            $result = $this->requestToClient($method, $url, $key, $data);

            return $this->success($result, 200, true);

        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    public function show($id)
    {
        try {
            if (cache(self::CACHE_EXPIRED_TOKEN)) {
                $accessToken = cache(self::CACHE_EXPIRED_TOKEN);
            } else {
                $accessToken = $this->getAccessToken();
            }

            //request show egift
            $method = 'POST';
            $url = config('common.egift.url_list');
            $key = 'Bearer '.$accessToken;
            $data = [
                'program_ids' => array($id),
            ];

            $result = $this->requestToClient($method, $url, $key, $data);

            return $this->success($result, 200, true);

        } catch (\Exception $e) {
            return $this->error($e, 400, true);
        }
    }

    private function requestToClient($method, $url, $key, $data = null)
    {
        $client = new Client();
        $res = $client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Authorization' => $key,
                ],
                'form_params' => $data,
            ]
        );

        return json_decode($res->getBody());
    }

    private function getAccessToken()
    {
        $apiKey = config('common.egift.api_key');
        $apiSecret = config('common.egift.api_secret');

        $encodedString = base64_encode($apiKey.':'.$apiSecret);
        $key = 'Basic '.$encodedString;
        $method = 'POST';
        $url = config('common.egift.url_token');

        $data = [
            'grant_type' => 'client_credentials',
            'scope' => 'offline_access',
        ];

        $r = $this->requestToClient($method, $url, $key, $data);

        $accessToken = $r->access_token;
        $expired = $r->expires_in;

        cache()->put(self::CACHE_EXPIRED_TOKEN, $accessToken, $expired);

        return $accessToken;
    }
}
