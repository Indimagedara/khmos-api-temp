<?php
// header('Access-Control-Allow-Origin: *');
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
    $empNumber = $_GET['id'];
    $query = "SELECT U.Fname,U.Lname,U.DevisionId,UG.* FROM Users U LEFT JOIN UserGroups UG ON UG.UgId = U.UserGroup
    WHERE U.EmpNumber = '$empNumber' AND U.Status == 1";

    if ($result = mysqli_query($con, $query)) {
        while ($rows = mysqli_fetch_assoc($result)) {
            $data['UserGroup'] = $rows['UserGroup'];
            $data['MealLimit'] = $rows['MealLimit'];
            $data['IsOverride'] = $rows['IsOverride'];
            $data['Fname'] = $rows['Fname'];
            $data['Lname'] = $rows['Lname'];
            $data['Division'] = $rows['DevisionId'];
        }
        echo json_encode($data);
    }
} else {
    echo json_encode([
        'status' => -1
    ]);
    die();
}
