<?php

if (!function_exists('admin_route_url')) {
    function admin_route_url($name, $parameters = [], $absolute = false)
    {
        $prefix = null == config('app.admin_route_prefix', null) ? '' : config('app.admin_route_prefix') . '.';
        return route($prefix . $name, $parameters, $absolute);
    }
}

if (!function_exists('route_resource_name')) {
    function route_resource_name($prefix, $name)
    {
        $prefix = null == $prefix ? '' : $prefix . '.';
        return [
            'store' => $prefix . $name . '.store',
            'index' => $prefix . $name . '.index',
            'create' => $prefix . $name . '.create',
            'show' => $prefix . $name . '.show',
            'destroy' => $prefix . $name . '.destroy',
            'update' => $prefix . $name . '.update',
            'edit' => $prefix . $name . '.edit',
        ];
    }
}


if (!function_exists('is_json_string')) {
    function is_json_string($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE ? true : false;
    }
}

if(! function_exists('first_letter_words')) {
    function first_letter_words($words, $count = 1)
    {
        $letter = preg_split("/[\s,_-]+/", $words);
        $first = '';
        for ($i=0; $i < $count; ++$i) {
            if(isset($letter[$i])) {
                $first .= strtoupper(mb_substr($letter[$i], 0, 1));
            }
        }

        return $first;
    }
}

if(! function_exists('clean_numeric')) {
    function clean_numeric($val, $symbol='%', $withRound = true, $decSeparator= '.')
    {
        $thousandSeparator = '.' === $decSeparator ? ',' : '.';
        $decValue = $decSeparator . '00';

        if($withRound) {
            return trim(str_replace([$thousandSeparator, $symbol, ' '], '', $val));
        }

        return trim(str_replace([$thousandSeparator, $decValue, $symbol, ' '], '', $val));
    }
}

if( ! function_exists('os_encrypt')) {
    function os_encrypt($data)
    {
        $key = config('app.key');
        $method = config('app.cipher');

        return openssl_encrypt($data, $method, $key);
    }
}

if( ! function_exists('os_decrypt')) {
    function os_decrypt($data)
    {
        $key = config('app.key');
        $method = config('app.cipher');

        return openssl_decrypt($data, $method, $key);
    }
}

