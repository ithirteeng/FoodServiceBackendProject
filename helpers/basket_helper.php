<?php

require_once "helpers/database_helper.php";
require_once "jwt_helper.php";

function getUserIdByToken($token): string
{
    global $link;
    $email = getEmailFromToken($token);
    $userData = pg_query($link, "select id from users where email = '$email'");
    return pg_fetch_assoc($userData)['id'];
}

function getBasketData($userId): array
{
    global $link;
    $result = array();
    $data = pg_query($link, "select *
                                    from dishes 
                                    inner join basket on dishes.id = basket.dish_id
                                    where basket.user_id = '$userId'");
    while ($row = pg_fetch_assoc($data)) {
        $totalPrice = (float)$row['price'] * (int)$row['amount'];
        $result[] = [
            "name" => $row['name'],
            "price" => (float)$row['price'],
            "totalPrice" => $totalPrice,
            "amount" => (int)$row['amount'],
            "image" => $row['image'],
            "id" => $row['dish_id'],
        ];
    }
    return $result;
}

function checkIfDishInBasket($dishId): bool {
    global $link;
    $data = pg_query($link, "select dish_id from basket where dish_id = '$dishId'");
    if (!pg_fetch_assoc($data)) {
        return false;
    } else {
        return true;
    }
}

function checkIfDishHasOrderId($dishId): bool {
    global $link;
    $data = pg_query($link, "select order_id from basket where dish_id = '$dishId'");
    $tableRow = pg_fetch_assoc($data);
    if ($tableRow['order_id'] == null) {
        return false;
    } else {
        return true;
    }
}
