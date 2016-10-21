<?php

namespace App\Contracts;

use Illuminate\Http\Request;

trait UriTrait
{

    /**
     * Extract request query string with following format.
     *      ex:
     *      ?filter=roles:administrator,crowd-source,vendor|status:active,non-active,suspended&blabla=scrum:master,diff
     *
     * @param \Illuminate\Http\Request $request
     * @param $key
     *
     * @return null|array|mixed
     */
    protected function uriExtractor(Request $request, $key)
    {
        if (!$request->has($key)) {
            return null;
        }
        if ($reqQuery = $request->query($key)) {
            return $this->uriList($reqQuery);
        }

        return null;
    }

    private function uriList($uri)
    {
        $lists = preg_split("/\|/", $uri);

        $result = [];
        foreach ($lists as $list) {
            $split = preg_split("/\:/", $list);

            $key = $split[0];
            $value = $split[1];
            $explodeValue = explode(',', trim($value));

            $result[$key] = (count($explodeValue) > 1) ? $explodeValue : $explodeValue[0];

        }

        return $result;
    }
}