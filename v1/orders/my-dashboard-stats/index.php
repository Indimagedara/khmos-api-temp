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
include('../../services/functions.php');

$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $empNumber = $_GET['id'];
    // $currentYear = 2024;
    // $currentMonth = 12;
    // $currentDate = 22;
    $currentYear = date("Y");
    $currentMonth = date("m");
    $currentDate = date("d");
    $nextMonthYear = $currentMonth == 12 ? $currentYear+1 : $currentYear;
    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth+1;
    $nextMonthAmount = 0;
    $nextMonthOrderCount = 0;

    $fromYear = $currentMonth == 1 ? $currentYear - 1: $currentYear;
    $toYear = $currentMonth == 12 ? $currentYear + 1: $currentYear;
    // $toYear = $currentYear;
    $fromMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    // $currentDate = 22;
    if($currentDate >= 15){
        $fromDateThisMonth = $currentYear.'-'.$currentMonth.'-16';
        $toDateThisMonth = $toYear.'-'.$nextMonth.'-15';
        $fromDateNextMonth = $toYear.'-'.($nextMonth).'-16';
        $toDateNexMonth = $toYear.'-'.($nextMonth+1).'-15';
    }else{
        $fromDateThisMonth = $fromYear.'-'.$fromMonth.'-16';
        $toDateThisMonth = $toYear.'-'.$currentMonth.'-15';
        $fromDateNextMonth = $toYear.'-'.$currentMonth.'-16';
        $toDateNexMonth = $toYear.'-'.$nextMonth.'-15';
    }
    // echo "current date :".$currentYear."-".$currentMonth."-".$currentDate."\n";
    // echo 'current month from :'.$fromDateThisMonth."\n";
    // echo 'current month to :'.$toDateThisMonth."\n";
    // echo 'next month from :'.$fromDateNextMonth."\n";
    // echo 'next month to :'.$toDateNexMonth."\n";

    $query = "SELECT COUNT(O.OrderId) AS NumberOfOrders, SUM(O.Amount) as TotalAmount FROM Orders O 
    LEFT JOIN Users U ON U.EmpNumber = O.EmployeeId
    WHERE O.EmployeeId = '$empNumber' AND U.Status = 1 AND
    STR_TO_DATE(O.OrderDate, '%m/%d/%Y') BETWEEN '$fromDateThisMonth' AND '$toDateThisMonth'
    ORDER BY O.OrderDate DESC";
    // echo $query;
    $query2 = "SELECT COUNT(O.OrderId) AS NumberOfnxtOrders, SUM(O.Amount) as TotalAmount2 FROM Orders O 
    LEFT JOIN Users U ON U.EmpNumber = O.EmployeeId
    WHERE O.EmployeeId = '$empNumber' AND U.Status = 1 AND
    STR_TO_DATE(OrderDate, '%m/%d/%Y') BETWEEN '$fromDateNextMonth' AND '$toDateNexMonth'
    ORDER BY O.OrderDate DESC";
    if ($result2 = mysqli_query($con, $query2)) {
        while ($rows2 = mysqli_fetch_assoc($result2)) {
            $nextMonthAmount = $rows2['TotalAmount2'];
            $nextMonthOrderCount = $rows2['NumberOfnxtOrders'];                      
        }
    }
    if ($result = mysqli_query($con, $query)) {
        while ($rows = mysqli_fetch_assoc($result)) {
            $data['MonthlyAmount'] = $rows['TotalAmount'];
            $data['MonthlyOrderCount'] = $rows['NumberOfOrders'];            
            $data['MealLimit'] = mealLimit($empNumber, $con);            
            $data['TotalOrders'] = myAllMealOrders($empNumber, $con);            
            $data['NextMonthAmount'] = $nextMonthAmount ? $nextMonthAmount : 0;            
            $data['NextMonthOrderCount'] = $nextMonthOrderCount;            
        }
        echo json_encode($data);
    }
} else {
    echo json_encode([
        'status' => -1
    ]);
    die();
}
