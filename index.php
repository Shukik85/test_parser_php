<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

include_once($_SERVER["DOCUMENT_ROOT"]."/Fivegear.php");
$gear = new Fivegear;

$result = $gear->get($_GET["brand"] ?? "");

if(isset($result["status"]))
{
	echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}