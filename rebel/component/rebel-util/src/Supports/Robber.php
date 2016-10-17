<?php
/**
 *
 * @author Pieter Lelaona a.k.a broklyn
 * @see http://
 * @license PRIVATE by Pieter Lelaona
 * Date - Time: 2/1/13 at 8:13 AM
 * This source codes may not be redistribute or modified in any kind of form
 * without permission from Pieter Lelaona (broklyn.gagah@gmail.com).
 */


namespace Rebel\Component\Util\Supports;

class Robber
{

    public function getUrlStatic(
        $url,
        $post_field = 0,
        &$cookie = '',
        $isRefererStatic = false,
        $urlReferer = null,
        $javascript_loop = 0,
        $is_post_json = false,
        $timeout = 30
    )
    {
        $url = str_replace("&amp;", '&', urldecode(trim($url)));
        if ($isRefererStatic) {
            $urlReferer = str_replace("&amp;", '&', urldecode(trim($urlReferer)));
        }

        $cookie_fname = tempnam("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.215 Safari/535.1");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_fname);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_fname);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

        if ($isRefererStatic) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: '.$urlReferer, 'Expect:'));
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: '.$url, 'Expect:'));
        }

        if ($is_post_json) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        }
        if ($post_field !== 0) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
        }
        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        curl_close($ch);

        $cookie_fhandle = fopen($cookie_fname, 'r');
        $cookie_fcontent = @fread($cookie_fhandle, filesize($cookie_fname));
        fclose($cookie_fhandle);
        unlink($cookie_fname);

        $cookie_response = $this->parseNetscapeCookie($cookie_fcontent);
        //if($cookie !== '')
        //$cookie .= '; ';
        foreach ($cookie_response as $cookie_line) {
            foreach ($cookie_line as $key => $val) {
                if ($key == 'name') {
                    $cookie .= $val . '=';
                }
                if ($key == 'value') {
                    $cookie .= $val . '; ';
                }
            }
        }
        $code = $response['http_code'];
        if ($response['http_code'] == 301 || $response['http_code'] == 302) {
            ini_set("user_agent", "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.215 Safari/535.1");
            if ($headers = get_headers($response['url'])) {
                foreach ($headers as $value) {
                    if (substr(strtolower($value), 0, 9) == "location:") {
                        if ($post_field !== '') {
                            if ($isRefererStatic == true) {
                                return $this->getUrl(trim(substr($value, 9, strlen($value))), $post_field, $cookie, true, $urlReferer);
                            } else {
                                return $this->getUrl(trim(substr($value, 9, strlen($value))), $post_field, $cookie);
                            }
                        } else {
                            if ($isRefererStatic == true) {
                                return $this->getUrlStatic(trim(substr($value, 9, strlen($value))), '', $cookie, true, $urlReferer);
                            } else {
                                return $this->getUrlStatic(trim(substr($value, 9, strlen($value))), '', $cookie);
                            }
                        }
                    }
                }
            }
        }

        if ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value)) && $javascript_loop < 5) {
            return $this->getUrlStatic($value[1], '', $cookie, $javascript_loop + 1);
        } else {
            return array($content, $response);
        }
    }

    /**
     * @param string $referrer
     * @param $url
     * @param $postField
     * @param string $cookie
     * @param int $javascriptLoop
     * @param bool $isPostJson
     * @param int $timeout
     * @return array
     */
    public function getUrl($referrer = '', $url, $postField, $cookie = '', $javascriptLoop = 0, $isPostJson = false, $timeout = 100)
    {
        set_time_limit(120);
        $url = str_replace("&amp;", "&", urldecode(trim($url)));
        $cookieFName = tempnam("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFName);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_fname);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($referrer == '') {
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        } else {
            curl_setopt($ch, CURLOPT_REFERER, $referrer);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        if ($isPostJson) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Content-length: '.strlen($postField), 'Accept: */*'));
        }

        if ($postField !== '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        curl_close($ch);

        $cookieFHandle = fopen($cookieFName, 'r');
        $cookieFContent = @fread($cookieFHandle, filesize($cookieFName));
        fclose($cookieFHandle);
        unlink($cookieFName);

        $cookieResponse = $this->parseNetscapeCookie($cookieFContent);
        if ($cookie !== '') {
            $cookie .= ';';
        }
        foreach ($cookieResponse as $cookieLine) {
            foreach ($cookieLine as $key => $val) {
                if ($key == 'name') {
                    $cookie .= $val . '=';
                }
                if ($key == 'value') {
                    $cookie .= $val . ';';
                }
            }
        }

        if ($response['http_code'] == 301 || $response['http_code'] == 302) {
            ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:27.0) Gecko/20100101 Firefox/27.0");
            if ($headers = get_headers($response['url'])) {
                foreach ($headers as $value) {
                    if (substr(strtolower($value), 0, 9) == "location:") {
                        if ($postField !== '') {
                            return $this->GetUrl(trim(substr($value, 9, strlen($value))), $postField, $cookie);
                        } else {
                            return $this->getUrl(trim(substr($value, 9, strlen($value))), '', $cookie);
                        }
                    }
                }
            }
        }

        if ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value)) && $javascriptLoop < 5) {
            return $this->getUrl($value[1], '', $cookie, ++$javascriptLoop);
        } else {
            return array($content, $response);
        }
    }

    /**
     * @param string $referrer
     * @param $url
     * @param $postField
     * @param string $cookie
     * @param int $javascriptLoop
     * @param bool $isPostJson
     * @param int $timeout
     * @return array
     */
    public function getUrlNotFollow($referrer = '', $url, $postField, $cookie = '', $javascriptLoop = 0, $isPostJson = false, $timeout = 100)
    {
        set_time_limit(120);
        $url = str_replace("&amp;", "&", urldecode(trim($url)));
        $cookieFName = tempnam("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFName);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_fname);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($referrer == '') {
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        } else {
            curl_setopt($ch, CURLOPT_REFERER, $referrer);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        if ($isPostJson) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Content-length: '.strlen($postField), 'Accept: */*'));
        }

        if ($postField !== '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        curl_close($ch);

        $cookieFHandle = fopen($cookieFName, 'r');
        $cookieFContent = @fread($cookieFHandle, filesize($cookieFName));
        fclose($cookieFHandle);
        unlink($cookieFName);

        $cookieResponse = $this->parseNetscapeCookie($cookieFContent);
        if ($cookie !== '') {
            $cookie .= ';';
        }
        foreach ($cookieResponse as $cookieLine) {
            foreach ($cookieLine as $key => $val) {
                if ($key == 'name') {
                    $cookie .= $val . '=';
                }
                if ($key == 'value') {
                    $cookie .= $val . ';';
                }
            }
        }

        if ($response['http_code'] == 301 || $response['http_code'] == 302) {
            ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
            if ($headers = get_headers($response['url'])) {
                foreach ($headers as $value) {
                    if (substr(strtolower($value), 0, 9) == "location:") {
                        if ($postField !== '') {
                            return $this->GetUrl(trim(substr($value, 9, strlen($value))), $postField, $cookie);
                        } else {
                            return $this->getUrl(trim(substr($value, 9, strlen($value))), '', $cookie);
                        }
                    }
                }
            }
        }

        if ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value)) && $javascriptLoop < 5) {
            return $this->getUrl($value[1], '', $cookie, ++$javascriptLoop);
        } else {
            return array($content, $response);
        }
    }


    /**
     * @param $cookieString
     * @return array
     */
    private function parseNetscapeCookie($cookieString)
    {
        $cookieStruct = array();
        // line by line
        $lines = explode("\n", $cookieString);
        // parse
        foreach ($lines as $key => $val) {
            $val = trim($val);
            if (substr($val, 0, 10) == '#HttpOnly_') {
                $val = str_ireplace('#HttpOnly_', '', $val);
            } else if (substr($val, 0, 1) == '#' || strlen($val) == 0) {
                continue;
            }
            $tabs = explode("\t", $val);
            $cookie['domain'] = $tabs[0];
            $cookie['flag'] = $tabs[1];
            $cookie['path'] = $tabs[2];
            $cookie['secure'] = $tabs[3];
            $cookie['expiration'] = $tabs[4];
            $cookie['name'] = $tabs[5];
            $cookie['value'] = $tabs[6];

            $cookieStruct[] = $cookie;
        }
        return $cookieStruct;
    }

    /**
     * @param $url
     * @param int $javascriptLoop
     * @param int $timeout
     * @return string
     */
    public function getCookie($url, $javascriptLoop = 0, $timeout = 100)
    {
        $url = str_replace("&amp;", "&", urldecode(trim($url)));
        $cookieFname = tempnam("/tmp", "CURLCOOKIE");
        $cookie = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFname);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        curl_close($ch);

        $cookie_fhandle = fopen($cookieFname, 'r');
        $cookie_fcontent = @fread($cookie_fhandle, filesize($cookieFname));
        fclose($cookie_fhandle);
        unlink($cookieFname);

        $cookie_response = $this->parseNetscapeCookie($cookie_fcontent);
        foreach ($cookie_response as $cookie_line) {
            foreach ($cookie_line as $key => $val) {
                if ($key == 'name') {
                    $cookie .= $val . '=';
                }
                if ($key == 'value') {
                    $cookie .= $val . ';';
                }
            }
        }
        if ($response['http_code'] == 301 || $response['http_code'] == 302) {
            ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
            if ($headers = $this->getCookie($response['url'])) {
                foreach ($headers as $value) {
                    if (substr(strtolower($value), 0, 9) == "location:") {
                        return $this->getCookie(trim(substr($value, 9, strlen($value))));
                    }
                }
            }
        }

        if ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value)) && $javascriptLoop < 5) {
            return $this->getCookie($value[1], ++$javascriptLoop);
        } else {
            return $cookie;
        }
    }


    function getUrlEncode($referer = '', $url, $post_field = '', &$cookie = '', $javascript_loop = 0, $is_post_json = false, $timeout = 100)
    {
        set_time_limit(120);

        $url = str_replace("&amp;", "&", urldecode(trim($url)));
        $cookie_fname = tempnam("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_fname);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_fname);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($referer == '') {
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        } else {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);

        if ($is_post_json) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-type: application/x-www-form-urlencoded;charset=UTF-8',
                'Content-length: '.strlen($post_field),
                'Accept: */*',
            ));
        }
        if ($post_field !== '') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $content = curl_exec($ch);
        $response = curl_getinfo($ch);
        curl_close($ch);

        $cookie_fhandle = fopen($cookie_fname, 'r');
        $cookie_fcontent = @fread($cookie_fhandle, filesize($cookie_fname));
        fclose($cookie_fhandle);
        unlink($cookie_fname);

        $cookie_response = $this->parseNetscapeCookie($cookie_fcontent);
        if ($cookie !== '') {
            $cookie .= ';';
        }
        foreach ($cookie_response as $cookie_line) {
            foreach ($cookie_line as $key => $val) {
                if ($key == 'name') {
                    $cookie .= $val . '=';
                }
                if ($key == 'value') {
                    $cookie .= $val . ';';
                }
            }
        }

        if ($response['http_code'] == 301 || $response['http_code'] == 302) {
            ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
            if ($headers = get_headers($response['url'])) {
                foreach ($headers as $value) {
                    if (substr(strtolower($value), 0, 9) == "location:") {
                        if ($post_field !== '') {
                            return $this->getUrlEncode(trim(substr($value, 9, strlen($value))), $post_field, $cookie);
                        } else {
                            return $this->getUrlEncode(trim(substr($value, 9, strlen($value))), '', $cookie);
                        }
                    }
                }
            }
        }

        if ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) ||
            preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value)) && $javascript_loop < 5)
        {
            $jLoop = $javascript_loop+1;
            return $this->getUrlEncode($value[1], '', $cookie, $jLoop);
        } else {
            return array($content, $response);
        }
    }
}
