<?php
require_once "helpers/validation_helper.php";
require_once "helpers/order_helper.php";
require_once "helpers/jwt_helper.php";
require_once "helpers/user_helper.php";

function checkRequestMethods($method): bool
{
    if ($method == "POST" || $method == "GET") {
        return true;
    } else {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    }
}

function getData($requestData): void
{

}

function postData($requestData): void
{
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];
    if (checkUserToken($token)) {
        global $link;
        if (getOrderValidationError($requestData)) {
            $data = pg_query($link, "select amount, dish_id, price from basket
                              join dishes d on d.id = basket.dish_id
                              where order_id is null");
            $totalPrice = 0;
            while ($row = pg_fetch_assoc($data)) {
                $totalPrice += $row['amount'] * $row['price'];
            }
            if ($totalPrice == 0) {
                setHttpStatus("400", "nothing to add to the order");
            } else {
                $deliveryTime = $requestData->body->deliveryTime;
                $deliveryTime = rtrim($deliveryTime, "Z");
                $address = $requestData->body->address;
                $orderTime = new DateTime();
                $orderTime = $orderTime->format(DATE_RFC3339_EXTENDED);
                $orderTime = explode("+", $orderTime)[0];
                $status = "InProcess";
                $userId = getUserIdByToken($token);
                pg_query($link, "insert into \"order\" (deliverytime, ordertime, address, price, status, user_id) 
                                    values ('$deliveryTime', '$orderTime', '$address', '$totalPrice', '$status', '$userId')");
                $orderId = pg_fetch_assoc(pg_query($link, "select id from \"order\" where deliverytime = '$deliveryTime' and user_id = '$userId'"))['id'];
                pg_query($link, "update basket set order_id = '$orderId' where order_id is null");

                setHttpStatus("200", "order created");
            }
        }
    }
}
