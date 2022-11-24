<?php
header('Content-type: application/json');
$x = '{ 
    "message": "hello"
    "sender" : {
        "id": 777,
        "name": "Ivan",
        "lines": [
            1, 2, 3, 4
        ]
    } 
}';

$message = array(
    'message' => "hello",
    'sender' => [
        'name' => "Ivan",
        'lastname' => "Gulevskii",
        'id' => 76678
    ]
);

$message['sender']['orders'] = [1, 2, 3];

$orderNames = ["sushi", "pizza", "bread", "shamov egor"];

foreach ($orderNames as $key => $value) {
    $message['sender']['orders'][$key] = [];
    $message['sender']['orders'][$key]['id'] = $key;
    $message['sender']['orders'][$key]['name'] = $value;
}

echo json_encode($message);


