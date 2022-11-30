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

//global $link;
//
//$res = pg_query($link, "SELECT * FROM test_table order by id");
//
//$message = array(
//    'account' => array()
//);
//
//if (!$res) {
//    echo "Не удалось выполнить запрос: (" . pg_last_error($link) . ") ";
//} else {
//    while ($row = pg_fetch_assoc($res)) {
//        $message['account'][] = [
//            'id' => $row["id"],
//            'login' => $row["login"],
//            'name' => $row["name"]
//        ];
//    }
//}
