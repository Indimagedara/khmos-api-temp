<?php
// header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    // header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}
include_once '../../../../config/db.php';


$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $employeeId = $_GET['id'];
    $currentYear = date("Y");
    $currentMonth = date("m");
    $query = "SELECT O.OrderDate, COUNT(O.OrderId) AS NumberOfOrders FROM Orders O LEFT JOIN Users U ON U.EmpNumber = O.EmployeeId WHERE YEAR(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $currentYear AND MONTH(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $currentMonth GROUP BY O.OrderDate ORDER BY O.OrderDate DESC";
    if ($result = mysqli_query($con, $query)) {
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
            $data[$i]['date'] = $rows['OrderDate'];
            $data[$i]['count'] = $rows['NumberOfOrders'];            
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