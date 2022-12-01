<?php

require_once "http_status_helper.php";

$link = pg_connect("host=localhost port=5432 dbname=food_service_db user=hits password=hits");

if (!$link) {
    setHttpStatus("500");
    exit("Unable to establish a connection to the database");
}
