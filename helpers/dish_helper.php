<?php

require_once "helpers/database_helper.php";

function getDishesInfo($requestData): array
{
    global $link;
    $parameters = $requestData->parameters;
    $sqlQuery = sqlQueryGenerator($parameters, "*");
    $data = pg_query($link, $sqlQuery);

    $result = array();

    while ($row = pg_fetch_assoc($data)) {
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

    return $result;
}


function getPaginationInfo($requestData): stdClass
{
    global $link;
    $result = new stdClass();
    $parameters = $requestData->parameters;
    $pageNumber = $parameters['page'] ?? null;
    $sqlQuery = sqlQueryGenerator($parameters, "count(*)");
    //echo $sqlQuery . PHP_EOL;

    $countArray = pg_fetch_assoc(pg_query($link, $sqlQuery));
    $amountOfElements = $countArray['count'];

    $result->size = 5;
    $result->count = (int)($amountOfElements / 5) + 1;
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

        if ($sortingType != null && $selectableString != "count(*)") {
            $sqlQuery = $sqlQuery . getCorrectSortingString($sortingType);
        }
    } else {
        if ($isVegetarian != null) {
            $sqlQuery = $sqlQuery . " where ";
            $sqlQuery = $sqlQuery . "(vegetarian = '$isVegetarian')";
        }
        if ($sortingType != null && $selectableString != "count(*)") {
            $sqlQuery = $sqlQuery . getCorrectSortingString($sortingType);
        }
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

