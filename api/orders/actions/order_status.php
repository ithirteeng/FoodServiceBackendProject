<?php

function checkRequestMethods($method): bool
{
    if ($method == "POST") {
        return true;
    } else {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    }
}

function postData($orderId): void
{
    global $link;

    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkUserToken($token)) {
        $userId = getUserIdByToken($token);
        if (checkOrderIdExisting($orderId, $userId)) {
            $isDeliveryConfirmed = pg_fetch_assoc(pg_query($link, "select status from \"order\" where id = '$orderId'"))['status'];
            if ($isDeliveryConfirmed == "Delivered") {
                setHttpStatus("400", "Order has already been delivered");
            } else {
                pg_query($link, "update \"order\" set status = 'Delivered' where id = '$orderId'");
                setHttpStatus("200", "Order '$orderId' is delivered");
            }
        } else {
            setHttpStatus("404", "Orders with this id do not exist");
        }
    }
}