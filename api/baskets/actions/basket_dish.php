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
            if (checkIfDishInBasket($dishId, $userId)) {
                pg_query($link, "update basket set amount = (amount + 1) where (dish_id = '$dishId' and user_id = '$userId' and order_id is null)");
            } else {
                pg_query($link, "insert into basket (user_id, dish_id, order_id, amount) 
                                    values ('$userId', '$dishId', null, 1)");
            }
            setHttpStatus("200", "basket updated");
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
        if (checkDishIdExisting($dishId)) {
            $userId = getUserIdByToken($token);
            if (checkIfDishInBasket($dishId, $userId)) {
                $deleteType = $requestData->parameters['increase'] ?? null;
                if ($deleteType == null) {
                    setHttpStatus("404", "'increase' parameter is forgotten");
                } else {
                    if ($deleteType == "true") {
                        if (getDishAmount($dishId, $userId) != 1) {
                            pg_query($link, "update basket set amount = (amount - 1) where (dish_id = '$dishId' and user_id = '$userId' and order_id is null)");
                        } else {
                            pg_query($link, "delete from basket where (dish_id = '$dishId' and user_id = '$userId' and order_id is null)");
                        }

                        setHttpStatus("200", "basket updated");
                    } else if ($deleteType == "false") {
                        pg_query($link, "delete from basket where (dish_id = '$dishId' and user_id = '$userId' and order_id is null)");

                        setHttpStatus("200", "basket updated");
                    } else {
                        setHttpStatus("404", "'increase' parameter must be true or false");
                    }
                }
            } else {
                setHttpStatus("404", "There is no such dish in the basket");
            }
        } else {
            setHttpStatus("404", "Dish with this id does not exist");
        }
    }
}