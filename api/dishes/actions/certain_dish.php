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
    if (checkDishIdExisting($id)) {
        echo json_encode(getDishInfo($id));
    } else {
        setHttpStatus("404", "Dishes with this id do not exist");
    }
}
