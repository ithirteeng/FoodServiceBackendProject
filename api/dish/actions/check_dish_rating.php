<?php

require_once "helpers/jwt_helper.php";
require_once "helpers/dish_helper.php";


function checkRequestMethods($method): bool
{
    if ($method != "GET") {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    } else {
        return true;
    }
}

function getData($id): void
{
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkIfTokenIsExpired($token)) {
        setHttpStatus("401", "The token has expired");
        addTokenToBlackList($token);
    } else if (checkIfTokenInBlackList($token)) {
        setHttpStatus("401", "User is unauthorized");
    } else {
        if (checkDishIdExisting($id)) {
            if (checkUserRatingExiting(getEmailFromToken($token), $id)) {
                echo "false";
            } else {
                echo "true";
            }
        } else {
            setHttpStatus("404", "Dishes with this id do not exist");
        }
    }
}
