<?php

function getData($requestData): void
{
    $result = new stdClass();
    $pagination = getPaginationInfo($requestData);
    if (checkPaginationCorrectness($pagination) and getCorrectDishParametersError($requestData)) {
        $result->dishes = getDishesInfo($requestData);
        // if ($pagination->count != 0) {
        $result->pagination = $pagination;
        //}
        echo json_encode($result);
    }

}

function checkPaginationCorrectness($pagination): bool
{
    $currentPage = $pagination->current;
    $amountOfPages = $pagination->count;
    if ($currentPage == 0 || $currentPage > $amountOfPages) {
        if ($amountOfPages == 0) {
            return true;
        } else {
            setHttpStatus("404", "page is incorrect");
            return false;
        }
    } else {
        return true;
    }
}

