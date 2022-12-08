<?php
require_once "helpers/jwt_helper.php";
require_once "helpers/dish_helper.php";
require_once "helpers/user_helper.php";

function checkRequestMethods($method): bool
{
    if ($method != "POST") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    } else {
        return true;
    }
}

function postData($requestData, $dishId): void
{
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkUserToken($token)) {
        if (checkDishIdExisting($dishId)) {
            if (canUserSetRating(getUserIdByToken($token), $dishId)) {
                setRatingForDish($requestData, getEmailFromToken($token), $dishId);
                setHttpStatus("200", "Rating has been set");
            } else {
                setHttpStatus("403", "Rating can't be put because user didn't order this dish");
            }
        } else {
            setHttpStatus("404", "Dishes with this id do not exist");
        }
    }
}
