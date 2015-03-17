<?php
/**
 * Airdoc
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if ($ext !== 'md' && file_exists('./'.$path)) {
    // let the server handle the request as-is
    return false;  
}

require(dirname(__FILE__).'/index.php');