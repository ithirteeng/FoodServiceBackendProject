<?php

function getData($requestData): void
{
    $result = new stdClass();
    $pagination = getPaginationInfo($requestData);
    if (checkPaginationCorrectness($pagination) and getCorrectDishParametersError($requestData)) {
        $result->dishes = getDishesInfo($requestData);
        $result->pagination = $pagination;
        echo json_encode($result);
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

