<?php

require_once "helpers/http_status_helper.php";
require_once "helpers/database_helper.php";
require_once "helpers/basket_helper.php";
require_once "helpers/user_helper.php";

function postData($dishId): void
{
    global $link;
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];
    if (checkUserToken($token)) {
        $userId = getUserIdByToken($token);
        if (checkDishIdExisting($dishId)) {
            if (checkIfDishInBasket($dishId)) {
                if (checkIfDishHasOrderId($dishId)) {
                    pg_query($link, "insert into basket (user_id, dish_id, order_id, amount) 
                                    values ($userId, $dishId, null, 1)");
                } else {
                    pg_query($link, "update basket set amount = (amount + 1)");
                }
            } else {
                pg_query($link, "insert into basket (user_id, dish_id, order_id, amount) 
                                    values ('$userId', '$dishId', null, 1)");
            }
        } else {
            setHttpStatus("404", "Dishes with this id do not exist");
        }
    }
}

function deleteData($requestData, $dishId): void
{
    global $link;
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkUserToken($token)) {
        $userId = getUserIdByToken($token);
        $deleteType = $requestData->parameters['increase'] ?? null;
        if ($deleteType == null) {

        } else {

        }
        if (checkDishIdExisting($dishId)) {
            if (checkIfDishInBasket($dishId)) {
                if (checkIfDishHasOrderId($dishId)) {

                } else {

                }
            } else {

            }
        } else {
            setHttpStatus("404", "Dishes with this id do not exist");
        }
    }
}