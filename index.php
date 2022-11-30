<?php
require_once "./helpers/database_helper.php";
require_once "./helpers/get_functions_helper.php";
require_once "./helpers/headers.php";

header('Content-type: application/json');

$url = $_GET['q'] ?? "";
$url = rtrim($url, '/');
$urlList = explode('/', $url);

$router = $urlList[1];
$requestMethod = getRequestMethod();
$requestData = getRequestData($requestMethod);

$filename = realpath(dirname(__FILE__)) . "/" . $urlList[0] . "/" . $router . ".php";


// echo $filename . PHP_EOL;

if (file_exists($filename)) {
    include_once "./api/" . $router . "/" . $router . ".php";
    route($requestMethod, $urlList, $requestData);
} else {
    setHttpStatus("404", "The page you are looking for can't be found");
}









