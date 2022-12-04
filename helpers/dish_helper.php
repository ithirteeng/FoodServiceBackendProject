<?php

require_once "helpers/database_helper.php";

$pageSize = 5;

function getDishesInfo($requestData): array
{
    global $link;
    global $pageSize;
    $parameters = $requestData->parameters;
    $sqlQuery = sqlQueryGenerator($parameters, "*");
    $data = pg_query($link, $sqlQuery);
    $page = $parameters['page'] ?? null;
    if ($page == null) {
        $page = 1;
    } else {
        $page = (int) $page;
    }

    $result = array();
    $count = 0;
    while ($row = pg_fetch_assoc($data)) {
        if ($count >= ($page * $pageSize - $pageSize) and $count < ($pageSize * $page)) {
            $isVegetarian = $row['vegetarian'] == 't';

            $result[] = [
                "name" => $row['name'],
                "description" => $row['description'],
                "price" => (float)$row['price'],
                "vegetarian" => $isVegetarian,
                "category" => $row['category'],
                "rating" => $row['rating'] ? (float)$row['rating'] : null,
                "id" => $row['id'],
            ];
        }
        $count += 1;
    }

    return $result;
}


function getPaginationInfo($requestData): stdClass
{
    global $link;
    global $pageSize;
    $result = new stdClass();
    $parameters = $requestData->parameters;
    $pageNumber = $parameters['page'] ?? null;
    $sqlQuery = sqlQueryGenerator($parameters, "count(*)");
    //echo $sqlQuery . PHP_EOL;

    $countArray = pg_fetch_assoc(pg_query($link, $sqlQuery));
    $amountOfElements = $countArray['count'];

    $result->size = $pageSize;
    $count = (int)($amountOfElements / 5);
    if ($count != $amountOfElements / 5) {
        $count += 1;
    }
    $result->count = $count;
    if ($pageNumber == null) {
        $result->current = 1;
    } else {
        $result->current = (int)$pageNumber;
    }

    return $result;
}

function sqlQueryGenerator($parameters, $selectableString): string
{
    $categories = $parameters['category'] ?? null;
    $isVegetarian = $parameters['vegetarian'] ?? null;
    $sortingType = $parameters['sorting'] ?? null;

    $sqlQuery = "select $selectableString from dishes";

    if ($categories != null) {
        $sqlQuery = $sqlQuery . " where (";
        if ($isVegetarian != null) {
            $sqlQuery = $sqlQuery . "vegetarian = '$isVegetarian' and ";
        }

        $addingString = "(";

        foreach ($categories as $value) {
            $addingString = $addingString . "category = '$value' or ";
        }
        $addingString = rtrim($addingString, " or");
        $addingString = $addingString . "))";

        $sqlQuery = $sqlQuery . $addingString;

    } else {
        if ($isVegetarian != null) {
            $sqlQuery = $sqlQuery . " where ";
            $sqlQuery = $sqlQuery . "(vegetarian = '$isVegetarian')";
        }
    }
    if ($sortingType != null && $selectableString != "count(*)") {
        $sqlQuery = $sqlQuery . getCorrectSortingString($sortingType);
    }

    return $sqlQuery;

}

function getCorrectSortingString($sortingType): string
{
    if ($sortingType == "NameAsc") {
        return " order by name ASC nulls first";
    } else if ($sortingType == "NameDesc") {
        return " order by name DESC nulls last";
    } else if ($sortingType == "PriceAsc") {
        return " order by price ASC nulls first";
    } else if ($sortingType == "PriceDesc") {
        return " order by price DESC nulls last";
    } else if ($sortingType == "RatingAsc") {
        return " order by rating ASC nulls first";
    } else if ($sortingType == "RatingDesc") {
        return " order by rating DESC nulls last";
    } else {
        return "";
    }
}

function getCorrectDishParametersError($requestData): bool
{
    global $link;
    $parameters = $requestData->parameters;
    $categories = $parameters['category'] ?? null;
    $isVegetarian = $parameters['vegetarian'] ?? null;
    $sortingType = $parameters['sorting'] ?? null;

    if ($sortingType != null and getCorrectSortingString($sortingType) == "") {
        setHttpStatus("404", "sortingType can be NameAsc, NameDesc, PriceAsc, PriceDesc, RatingAsc, RatingDesc");
        return false;
    } else if ($isVegetarian != null and ($isVegetarian != "true" and $isVegetarian != "false")) {
        setHttpStatus("404", "vegetarian must be true or false");
        return false;
    } else if ($categories != null) {
        foreach ($categories as $category) {
            if (!pg_fetch_assoc(
                pg_query($link, "select type from dish_category where type = '$category'")
            )) {
                setHttpStatus("404", "category '$category' doesn't exist");
                return false;
            }
        }
        return true;
    } else {
        return true;
    }
}

function checkDishIdExisting($id): bool
{
    global $link;
    $regex = "/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i";
    if (preg_match($regex, $id)) {

        if (pg_fetch_assoc(
            pg_query($link, "select id from dishes where id = '$id'")
        )) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

