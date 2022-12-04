<?php

require_once "helpers/http_status_helper.php";
require_once "helpers/database_helper.php";
require_once "helpers/jwt_helper.php";
require_once "helpers/dish_helper.php";

function route($method, $urlList, $requestData): void
{
    if (count($urlList) == 2) {
        if ($method == "GET") {
            getDishesData($requestData);
        } else {
            setHttpStatus("405", "Method " . $method . " is not allowed");
        }
    } else if (count($urlList) == 3) {
        require_once "actions/certain_dish.php";
        if (checkDishIdExisting($urlList[2])) {
            getData($requestData, $urlList[2]);
        } else {
            setHttpStatus("404", "Dishes with this id do not exist");
        }
    } else if (count($urlList) == 4) {
        if ($urlList[3] == "rating") {
            require_once "actions/rating.php";
            if (checkDishIdExisting($urlList[2])) {
                getData($requestData, $urlList[2]);
            } else {
                setHttpStatus("404", "Dishes with this id do not exist");
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else if (count($urlList) == 5) {
        if ($urlList[3] == "rating" && $urlList[4] == "check") {
            require_once "actions/check.php";
            if (checkRequestMethods($method)) {
                if (checkDishIdExisting($urlList[2])) {
                    getData($requestData, $urlList[2]);
                } else {
                    setHttpStatus("404", "Dishes with this id do not exist");
                }
            }
        } else {
            setHttpStatus("404", "The page you are looking for can't be found");
        }
    } else {
        setHttpStatus("404", "The page you are looking for can't be found");
    }
}

function checkDishIdExisting($id): bool
{
    global $link;
    $regex = "/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i";
    if (preg_match($regex, $id)) {

        if (pg_fetch_assoc(
            pg_query($link, "select id from dishes where id = '$id'")
        )) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

function getDishesData($requestData): void
{
    $parameters = $requestData->parameters;
    getPagesInfo($requestData);

}
