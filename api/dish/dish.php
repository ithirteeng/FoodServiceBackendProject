<?php

require_once "helpers/http_status_helper.php";
require_once "helpers/database_helper.php";
require_once "helpers/jwt_helper.php";

function route($method, $urlList, $requestData): void
{
    if (count($urlList) == 2) {
        if (checkRequestMethods($method)) {
            getDishesData($requestData);
        }
    } else if (count($urlList) == 3) {
        require_once "actions/certain_dish.php";
        if (checkRequestMethods($method)) {
            getData($requestData, $urlList[2]);
        }
    } else if (count($urlList) == 4) {
        if ($urlList[3] == "rating") {
            require_once "actions/rating.php";
            if (checkRequestMethods($method)) {
                getData($requestData, $urlList[2]);
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else if (count($urlList) == 5) {
        if ($urlList[3] == "rating" && $urlList[4] == "check") {
            require_once "actions/check.php";
            if (checkRequestMethods($method)) {
                getData($requestData, $urlList[2]);
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }
}

function getDishesData($requestData): void
{

}

function checkRequestMethods($method): bool
{
    if ($method != "GET") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return true;
    } else {
        return false;
    }
}