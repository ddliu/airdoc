<?php
$root = dirname(__FILE__);
require($root.'/airdoc.php');
$configs = include($root.'/config.php');
$airdoc = new Airdoc($configs);
$airdoc->serve();