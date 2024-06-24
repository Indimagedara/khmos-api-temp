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
    $type = $_GET['type'];
    $query = "SELECT * FROM SiteData WHERE SettingType = '$type' ORDER BY SdId DESC";
    if ($result = mysqli_query($con, $query)) {
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
            $data[$i]['Setting'] = $rows['Setting'];
            $data[$i]['SettingValue'] = $rows['SettingValue'];
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
