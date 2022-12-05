<?php
require_once "helpers/basket_helper.php";
require_once "helpers/http_status_helper.php";
require_once "helpers/user_helper.php";

function getData(): void
{
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];
    if (checkUserToken($token)) {
        $userId = getUserIdByToken($token);
        echo json_encode(getBasketData($userId));
    }
}
