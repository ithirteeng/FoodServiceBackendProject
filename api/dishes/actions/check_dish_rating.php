<?php

require_once "helpers/jwt_helper.php";
require_once "helpers/dish_helper.php";
require_once "helpers/user_helper.php";

function checkRequestMethods($method): bool
{
    if ($method != "GET") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    } else {
        return true;
    }
}

function getData($dishId): void
{
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkDishIdExisting($dishId)) {
        if (!checkUserTokenForDish($token)) {
            echo "false";
        } else {
            $userId = getUserIdByToken($token);
            echo $userId . PHP_EOL;
            if (canUserSetRating($userId, $dishId)) {
                echo "true";
            } else {
                echo "false";
            }
        }
    } else {
        setHttpStatus("404", "Dishes with this id do not exist");
    }

}
