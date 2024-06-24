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
    $employeeId = $_GET['id'];
    $currentYear = date("Y");
    $currentMonth = date("m");
    $currentDate = date("d");
    $fromYear = $currentMonth == 1 ? $currentYear - 1: $currentYear;
    $toYear = $currentMonth == 12 ? $currentYear + 1: $currentYear;
    $fromMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth+1;

    if($currentDate >= 15){
        $fromDate = $currentYear.'-'.$currentMonth.'-16';
        $toDate = $toYear.'-'.$nextMonth.'-15';
    }else{
        $fromDate = $fromYear.'-'.$fromMonth.'-16';
        $toDate = $toYear.'-'.$currentMonth.'-15';
    }
    $query = "SELECT O.OrderDate, COUNT(O.OrderId) AS NumberOfOrders FROM Orders O LEFT JOIN Users U ON U.EmpNumber = O.EmployeeId WHERE O.EmployeeId = '$employeeId' AND
    STR_TO_DATE(OrderDate, '%m/%d/%Y') BETWEEN '$fromDate' AND '$toDate' 
    GROUP BY O.OrderDate ORDER BY O.OrderDate DESC";
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