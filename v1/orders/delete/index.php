<?php
//header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
    exit;
}
require '../../../config/db.php';
include('../../services/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//Get posted data
$postData = file_get_contents("php://input");
    if(isset($postData) && !empty($postData)){
        //Extract the data
        $request = json_decode($postData);
    
        //Validate data
        if(trim($request->orderId)===''){
            return http_response_code(400);
        }
                                            
        //Sanitize
        $orderId = mysqli_real_escape_string($con, trim($request->orderId));
        $employeeId = mysqli_real_escape_string($con, trim($request->employeeId));

        //Query
        // $query = "SELECT COUNT(O.OrderId) AS NumberOfOrders, SUM(O.Amount) as TotalAmount FROM Orders O 
        // WHERE O.EmployeeId = '$empNumber' AND
        // STR_TO_DATE(O.OrderDate, '%m/%d/%Y') BETWEEN '$fromDateThisMonth' AND '$toDateThisMonth'
        // ORDER BY O.OrderDate DESC";
        // $query2 = "SELECT COUNT(O.OrderId) AS NumberOfnxtOrders, SUM(O.Amount) as TotalAmount2 FROM Orders O 
        // WHERE O.EmployeeId = '$empNumber' AND 
        // STR_TO_DATE(OrderDate, '%m/%d/%Y') BETWEEN '$fromDateNextMonth' AND '$toDateNexMonth'
        // ORDER BY O.OrderDate DESC";
        // if ($result2 = mysqli_query($con, $query2)) {
        //     while ($rows2 = mysqli_fetch_assoc($result2)) {
        //         $nextMonthAmount = $rows2['TotalAmount2'];
        //         $nextMonthOrderCount = $rows2['NumberOfnxtOrders'];                      
        //     }
        // }
        // if ($result = mysqli_query($con, $query)) {
        //     while ($rows = mysqli_fetch_assoc($result)) {
        //         $monthlyAmount = $rows['TotalAmount'];
        //         $monthlyOrderCount = $rows['NumberOfOrders'];            
        //         $mealLimit = mealLimit($empNumber, $con);            
        //         $nextMonthAmount = $nextMonthAmount ? $nextMonthAmount : 0;            
        //         $nextMonthOrderCount = $nextMonthOrderCount;            
        //     }
        //     echo json_encode($data);
        // }

        $getQry = "";
        $query = "DELETE FROM `Orders` WHERE `EmployeeId`='$employeeId' AND `OrderId`='$orderId' ";
        if($results = mysqli_query($con,$query)){
            $n = mysqli_affected_rows($con);
            if($n == 1){
                http_response_code(201);
                $cusGroups = [
                    'status'=> 1
                ];
                echo json_encode($cusGroups);
            }else{
                http_response_code(400);
                $customerBasics = [
                    'status'=> 0
                ];
                echo json_encode($customerBasics);
            }
        }else{
            http_response_code(422);
            echo 'There is an error on query!';
        }
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Invalid request method.',
    ]);
    http_response_code(405); 
}
