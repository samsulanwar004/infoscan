<?php

namespace Rebel\Component\Util\Supports;

/**
 * travelproject Created by Pieter Lelaona.
 * @Author: broklyn <broklyn.gagah@gmail.com>
 * Date: 4/20/13
 * Time: 5:55 PM
 * Don\'t be a DONKEY DICK...!!!
 */

class SimpleDom
{

    protected static $dom;

    protected static $output;

    public function __construct()
    {
        require_once __DIR__ . '/simple_html_dom.php';
    }

    protected function getHtml($html)
    {
        if (false == $html) {
            return false;
        }

        return str_get_html($html);
    }
}
