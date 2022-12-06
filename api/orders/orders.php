<?php
function route($method, $urlList, $requestData): void
{
    if (count($urlList) == 2) {
        require_once "actions/order.php";
        if (checkRequestMethods($method)) {
            if ($method == "POST") {
                postData($requestData);
            } else {
                getData($requestData);
            }
        }
    } else if (count($urlList) == 3) {
        require_once "actions/order_info.php";
        if (checkRequestMethods($method)) {
            getData($requestData, $urlList[2]);
        }
    } else if (count($urlList) == 4) {
        if ($urlList[3] == "check") {
            require_once "actions/order_status.php";
            if (checkRequestMethods($method)) {
                postData($requestData, $urlList[2]);
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }
}
