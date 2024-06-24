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
require '../../../config/db.php';
$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM Locations ORDER BY `Location` DESC";
    if ($result = mysqli_query($con, $query)) {
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
            $data[$i]['LocId'] = $rows['LocId'];
            $data[$i]['Location'] = $rows['Location'];
            $data[$i]['Floor'] = $rows['Floor'];
            $i++;
        }
        echo json_encode($data);
    }
} else {
    echo json_encode([
        'status' => -1
    ]);
    die();
}
