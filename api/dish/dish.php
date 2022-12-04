<?php

require_once "helpers/http_status_helper.php";
require_once "helpers/database_helper.php";
require_once "helpers/jwt_helper.php";
require_once "helpers/dish_helper.php";

function route($method, $urlList, $requestData): void
{
    if (count($urlList) == 2) {
        require_once "actions/all_dishes.php";
        if ($method == "GET") {
            getData($requestData);
        } else {
            setHttpStatus("405", "Method " . $method . " is not allowed");
        }
    } else if (count($urlList) == 3) {
        require_once "actions/certain_dish.php";
        if (checkRequestMethods($method)) {
            getData($urlList[2]);
        }
    } else if (count($urlList) == 4) {
        if ($urlList[3] == "rating") {
            require_once "actions/set_dish_rating.php";
            if (checkRequestMethods($method)) {
                postData($requestData, $urlList[2]);
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else if (count($urlList) == 5) {
        if ($urlList[3] == "rating" && $urlList[4] == "check") {
            require_once "actions/check_dish_rating.php";
            if (checkRequestMethods($method)) {
                getData($urlList[2]);
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }
}


