<?php

require_once "helpers/order_helper.php";
require_once "helpers/jwt_helper.php";
require_once "helpers/user_helper.php";
require_once "helpers/validation_helper.php";

function checkRequestMethods($method): bool
{
    if ($method == "GET") {
        return true;
    } else {
        setHttpStatus("405", "Method " . $method . " is not allowed");
        return false;
    }
}

function getData($orderId): void
{
    global $link;

    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkUserToken($token)) {
        $userId = getUserIdByToken($token);
        if (checkOrderIdExisting($orderId, $userId)) {
            $data = pg_query($link, "select * from \"order\" where id = '$orderId' and user_id = '$userId'");
            $result = array();
            while ($orderRow = pg_fetch_assoc($data)) {
                $dish_data = pg_query($link, "select amount, dish_id, price, name, image from basket
                              join dishes d on d.id = basket.dish_id
                              where order_id = '$orderId'");
                $dishes = array();
                while ($basketRow = pg_fetch_assoc($dish_data)) {
                    $totalPrice = (int) $basketRow['amount'] * (float) $basketRow['price'];
                    $dishes[] = [
                        "id" => $basketRow['dish_id'],
                        "name" => $basketRow['name'],
                        "price" => (float) $basketRow['price'],
                        "totalPrice" => $totalPrice,
                        "amount" => (int) $basketRow['amount'],
                        "image" => $basketRow['image']

                    ];
                }
                $result[] = [
                    "id" => $orderRow["id"],
                    "deliveryTime" => convertDateToCorrectForm($orderRow["deliverytime"]),
                    "orderTime" => convertDateToCorrectForm($orderRow["ordertime"]),
                    "status" => $orderRow["status"],
                    "price" => (float) $orderRow["price"],
                    "dishes" => $dishes,
                    "address" => $orderRow['address']
                ];
            }
            echo json_encode($result);
        } else {
            setHttpStatus("404", "Orders with this id do not exist");
        }
    }

}