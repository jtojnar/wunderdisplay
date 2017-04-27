<?php

require_once __DIR__ . '/vendor/autoload.php';

use jtojnar\Wunderdisplay\Wunderdisplay;

$wd = new Wunderdisplay([
	'client_id' => '',
	'client_secret' => '',
	'access_token' => '',
	'list_id' => 1337,
	'title' => 'to-do list',
]);

$wd->run();
