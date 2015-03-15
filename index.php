<?php
/**
 * @author Liu Dong <ddliuhb@gmail.com>
 * @license MIT
 * @copyright 2015 Liu Dong
 */

// ini_set('display_errors', true);
// error_reporting(E_ALL^E_NOTICE);
ini_set('display_errors', false);
error_reporting(E_ALL^E_NOTICE);
$root = dirname(__FILE__);
require($root.'/airdoc.php');
$configs = include($root.'/config.php');
$airdoc = new Airdoc($configs);
$airdoc->serve();