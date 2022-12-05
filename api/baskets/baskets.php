<?php

require_once "helpers/dish_helper.php";
require_once "helpers/database_helper.php";

function route($method, $urlList, $requestData): void
{
    global $link;
    if (count($urlList) == 2) {
        require_once "actions/basket_info.php";
        if ($method == 'GET') {
            getData();
        } else {
            setHttpStatus("405", "Method " . $method . " is not allowed");
        }
    } else if (count($urlList) == 4) {
        if ($urlList[2] == "dishes" and checkDishIdExisting($urlList[3])) {
            require_once "actions/basket_dish.php";
            if ($method == "POST") {
                postData($requestData, $urlList[3]);
            } else if ($method == "DELETE") {
                deleteData($requestData, $urlList[3]);
            } else {
                setHttpStatus("405", "Method " . $method . " is not allowed");
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }
}