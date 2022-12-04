<?php

require_once "helpers/database_helper.php";

function getDishesInfo($requestData): array {
    global $link;
    $parameters = $requestData->parameters;
    $sqlQuery = sqlQueryGenerator($parameters, "*");
    $data = pg_query($link, $sqlQuery);

    $result = array();

    while ($row = pg_fetch_assoc($data)) {
        $isVegetarian = $row['vegetarian'] == 't';
        $dishRating = countDishRating($row['id']);
        $result[] = [
            "name" => $row['name'],
            "description" => $row['description'],
            "price" => $row['price'],
            "vegetarian" => $isVegetarian,
            "category" => $row['category'],
            "rating" => $dishRating,
            "id" => $row['id'],
        ];
    }

    return $result;
}

function countDishRating($dishId): float {
    global $link;
    $result = pg_fetch_assoc(
        pg_query($link, "select avg(rating.rating) from rating where dish_id = '$dishId'")
    );
    return $result['avg'] ?? 0.0;
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
    $result->current = ($pageNumber ?? 1);

    return $result;
}

function sqlQueryGenerator($parameters, $selectableString): string
{
    $categories = $parameters['category'] ?? null;
    $isVegetarian = $parameters['vegetarian'] ?? null;

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

    return $sqlQuery;

}

