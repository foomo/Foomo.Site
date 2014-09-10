<?php

if (isset($_GET['format'])) {
	$format = $_GET['format'];
} else {
	$format = 'json';
}

$data = \Schild\ContentServer\Export::crawl();
switch($format) {
	case 'json':
		header('Content-Type: application/json');
		if(defined('JSON_PRETTY_PRINT')) {
			echo json_encode($data, JSON_PRETTY_PRINT);
		} else {
			echo json_encode($data);
		}
		break;
	case 'text':
		header('Content-Type: text/plain;charset=utf-8;');
		ini_set("html_errors", "Off");
		var_dump($data);
		break;
	default:
		trigger_error("bad request - header my ass ;)", E_USER_ERROR);
}