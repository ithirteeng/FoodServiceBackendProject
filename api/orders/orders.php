<?php

require_once "helpers/http_status_helper.php";
require_once "helpers/user_helper.php";
require_once "helpers/order_helper.php";

function route($method, $urlList, $requestData): void
{
    if (count($urlList) == 2) {
        require_once "actions/order.php";
        if (checkRequestMethods($method)) {
            if ($method == "POST") {
                postData($requestData);
            } else {
                getData();
            }
        }
    } else if (count($urlList) == 3) {
        require_once "actions/order_info.php";
        if (checkRequestMethods($method)) {
            getData($urlList[2]);
        }
    } else if (count($urlList) == 4) {
        if ($urlList[3] == "status") {
            require_once "actions/order_status.php";
            if (checkRequestMethods($method)) {
                postData($urlList[2]);
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }
}
