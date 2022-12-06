<?php

require_once "./helpers/database_helper.php";
require_once "./helpers/get_functions_helper.php";
require_once "./helpers/http_status_helper.php";

date_default_timezone_set("Asia/Krasnoyarsk");
header('Content-type: application/json');

$url = $_GET['q'] ?? "";
$url = rtrim($url, '/');
$urlList = explode('/', $url);

$router = $urlList[1];
$requestMethod = getRequestMethod();
$requestData = getRequestData($requestMethod);

if ($router == "basket") {
    $router = "baskets";
} else if ($router == "dish") {
    $router = "dishes";
} else if ($router == "order") {
    $router = "orders";
}
$filename = realpath(dirname(__FILE__)) . "/" . $urlList[0] . "/" . $router . "/" . $router . ".php";

//echo $filename . PHP_EOL;

if (file_exists($filename)) {
    require_once $filename;
    try {
        route(getRequestMethod(), $urlList, $requestData);
    } catch (Exception $e) {
        setHttpStatus("505");
        exit;
    }
} else {
    setHttpStatus("404", "The page you are looking for can't be found");
}










