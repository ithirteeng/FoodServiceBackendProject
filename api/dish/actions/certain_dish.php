<?php

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
            echo json_encode(getDishInfo($id));
        } else {
            setHttpStatus("404", "Dishes with this id do not exist");
        }
    }

}
