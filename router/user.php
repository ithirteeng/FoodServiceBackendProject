<?php

function route($method, $urlList, $requestData) {
    switch ($method) {
        case "GET":
            break;
        case "POST":
            $link = pg_connect("host=localhost port=5432 dbname=food_service_db user=hits password=hits");
            $users = pg_query($link, "SELECT id FROM test_table WHERE login = '$requestData->login'");
            $data = pg_fetch_assoc($users);

            if (is_null($users)) {
            } else {
                echo "user exists";
            }
            break;
    }
}
