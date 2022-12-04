<?php

require_once "helpers/database_helper.php";

function getPagesInfo($requestData): void
{
    global $link;

    $parameters = $requestData->parameters;
    //echo json_encode($parameters) . PHP_EOL;

    $sqlQuery = sqlQueryGenerator($parameters, "count(*)");
    echo $sqlQuery . PHP_EOL;
    $countArray = pg_fetch_assoc(pg_query($link, $sqlQuery));
    $amountOfElements = $countArray['count'];
    echo $amountOfElements;

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
        $addingString = rtrim($addingString, " or ");
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

