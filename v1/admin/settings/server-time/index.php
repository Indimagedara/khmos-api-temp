<?php
//header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    //header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}
require '../../../../config/db.php';
$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // $serverTime = "2024-01-10 10:10:27";
    $serverTime = date("Y-m-d H:i:s");
    echo json_encode([
        'time' => $serverTime
    ]);
} else {
    echo json_encode([
        'status' => -1
    ]);
    die();
}