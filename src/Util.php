<?php
/**
 * Airdoc
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */

namespace ddliu\airdoc;

use ddliu\normurl\Url;

class Util {
    public static function joinPath($a, $b) {
        return str_replace('//', '/', $a.'/'.$b);
    }

    public static function getExt($path) {
        $dotpos = strrpos($path, '.');
        if ($dotpos === false) {
            return '';
        }

        return substr($path, $dotpos + 1);
    }

    public static function getBasename($filename){
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    public static function getUrlInfo() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $rootUrl = '/';

        $prefix = 'http://localhost/';
        $prefixLen = strlen($prefix);
        $path = $prefix.$path;
        $path = Url::normalize($path);

        $path = substr($path, $prefixLen);
        
        if ($path === '') {
            $path = '/';
        }
        
        $path = urldecode($path);

        return array($rootUrl, $path);
    }
}