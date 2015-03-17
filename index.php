<?php
/**
 * Airdoc
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */

use ddliu\airdoc\App;

ini_set('display_errors', false);
error_reporting(E_ALL^E_NOTICE);
require __DIR__.'/vendor/autoload.php';
$configs = include(__DIR__.'/config.php');

$app = new App($configs);
$app->run();