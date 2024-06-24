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
include_once '../../../config/db.php';

$data = [];
$thisMonthOrders = 0;
$thisMonthAmount = 0;
$prevMonthOrders = 0;
$prevMonthAmount = 0;
$nextMonthAmount = 0;
$nextMonthOrders = 0;

$currentYear = date("Y");
$currentMonth = date("m");
$todayDate = date('m/d/Y');
$today = new DateTime();
$yesterday = clone $today;
$yesterday->modify('-1 day');

$yesterdayDate = $yesterday->format('Y-m-d');

$todayAmount = 0;
$todayOrders = 0;

$nextMonthYear = $currentMonth == 12 ? $currentYear+1 : $currentYear;
$nextMonth = $currentMonth == 12 ? 1 : $currentMonth+1;
$prevMonthYear = $currentMonth == 1 ? $currentYear-1 : $currentYear;
$prevMonth = $currentMonth == 1 ? 12 : $currentMonth-1;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $usersQuery = "SELECT * FROM Users WHERE IsDeleted = 0";
    $users = mysqli_num_rows(mysqli_query($con, $usersQuery));

    $adminUsersQuery = "SELECT * FROM Users WHERE IsDeleted = 0 AND IsAdmin = 1";
    $adminUsers = mysqli_num_rows(mysqli_query($con, $adminUsersQuery));

    $totalOrdersQuery = "SELECT * FROM Orders";
    $totalOrders = mysqli_num_rows(mysqli_query($con, $totalOrdersQuery));

    $thisMonthOrderStatsQuery = "SELECT COUNT(O.OrderId) AS NumberOfOrders, SUM(O.Amount) as TotalAmount FROM Orders O 
    WHERE YEAR(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $currentYear AND MONTH(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $currentMonth ORDER BY O.OrderDate DESC";
    if ($thisMonthOrderStatsResult = mysqli_query($con, $thisMonthOrderStatsQuery)) {
        while ($thisMonthOrderStatsRows = mysqli_fetch_assoc($thisMonthOrderStatsResult)) {
            $thisMonthAmount = $thisMonthOrderStatsRows['TotalAmount'];
            $thisMonthOrders = $thisMonthOrderStatsRows['NumberOfOrders'];                      
        }
    }

    $prevMonthOrderStatsQuery = "SELECT COUNT(O.OrderId) AS NumberOfOrders, SUM(O.Amount) as TotalAmount FROM Orders O 
    WHERE YEAR(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $prevMonthYear AND MONTH(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $prevMonth ORDER BY O.OrderDate DESC";
    if ($prevMonthOrderStatsResult = mysqli_query($con, $prevMonthOrderStatsQuery)) {
        while ($prevMonthOrderStatsRows = mysqli_fetch_assoc($prevMonthOrderStatsResult)) {
            $prevMonthAmount = $prevMonthOrderStatsRows['TotalAmount'];
            $prevMonthOrders = $prevMonthOrderStatsRows['NumberOfOrders'];                      
        }
    }

    $nextMonthOrderStatsQuery = "SELECT COUNT(O.OrderId) AS NumberOfOrders, SUM(O.Amount) as TotalAmount FROM Orders O 
    WHERE YEAR(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $nextMonthYear AND MONTH(STR_TO_DATE(O.OrderDate, '%m/%d/%Y')) = $nextMonth ORDER BY O.OrderDate DESC";
    if ($nextMonthOrderStatsResult = mysqli_query($con, $nextMonthOrderStatsQuery)) {
        while ($nextMonthOrderStatsRows = mysqli_fetch_assoc($nextMonthOrderStatsResult)) {
            $nextMonthAmount = $nextMonthOrderStatsRows['TotalAmount'];
            $nextMonthOrders = $nextMonthOrderStatsRows['NumberOfOrders'];                      
        }
    }
    
    $todayOrdersQuery = "SELECT COUNT(o.OrderId) AS OrderCount, SUM(o.Amount) as OrderAmount FROM Orders o WHERE o.OrderDate = '$todayDate'";
    if ($todayOrderStatsResult = mysqli_query($con, $todayOrdersQuery)) {
        while ($todayOrderStatsRows = mysqli_fetch_assoc($todayOrderStatsResult)) {
            $todayAmount = $todayOrderStatsRows['OrderAmount'];
            $todayOrders = $todayOrderStatsRows['OrderCount'];                      
        }
    }


    $yesterdayOrdersQuery = "SELECT COUNT(o.OrderId) AS OrderCount, SUM(o.Amount) as OrderAmount FROM Orders o WHERE o.OrderDate = '$yesterdayDate'";
    if ($yesterdayOrderStatsResult = mysqli_query($con, $yesterdayOrdersQuery)) {
        while ($yesterdayOrderStatsRows = mysqli_fetch_assoc($yesterdayOrderStatsResult)) {
            $yesterdayAmount = $yesterdayOrderStatsRows['OrderAmount'];
            $yesterdayOrders = $yesterdayOrderStatsRows['OrderCount'];                      
        }
    }

    echo json_encode([
        'status' => 1,
        'data' => [
            'users' => $users, 
            'adminUsers' => $adminUsers, 
            'totalOrders' => $totalOrders, 
            'thisMonthAmount' => $thisMonthAmount, 
            'thisMonthOrders' => $thisMonthOrders,
            'prevMonthAmount' => $prevMonthAmount, 
            'prevMonthOrders' => $prevMonthOrders,
            'nextMonthAmount' => $nextMonthAmount, 
            'nextMonthOrders' => $nextMonthOrders,
            'todayOrders' => $todayOrders,
            'todayAmount' => $todayAmount,
            'yesterdayOrders' => $yesterdayOrders,
            'yesterdayAmount' => $yesterdayAmount,
        ],
        'message' => '',
    ]);
} else {
    echo json_encode([
        'status' => 0
    ]);
    die();
}
