<?php

function getData($requestData): void
{
    $authorization = getallheaders()["Authorization"];
    $token = explode(" ", $authorization)[1];

    if (checkIfTokenIsExpired($token)) {
        setHttpStatus("401", "The token has expired");
        addTokenToBlackList($token);
    } else if (checkIfTokenInBlackList($token)) {
        setHttpStatus("401", "User is unauthorized");
    } else {
        $result = new stdClass();
        $pagination = getPaginationInfo($requestData);
        if (checkPaginationCorrectness($pagination) and getCorrectDishParametersError($requestData)) {
            $result->dishes = getDishesInfo($requestData);
            $result->pagination = $pagination;
            echo json_encode($result);
        }
    }
}

function checkPaginationCorrectness($pagination): bool
{
    $currentPage = $pagination->current;
    $amountOfPages = $pagination->count;
    if ($currentPage == 0 || $currentPage > $amountOfPages) {
        setHttpStatus("404", "page is incorrect");
        return false;
    } else {
        return true;
    }
}

