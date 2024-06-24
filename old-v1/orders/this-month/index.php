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
    $query = "SELECT O.Status as OrderStatus,L.Location as LocationName,O.*,D.*,U.* FROM Orders O 
    INNER JOIN Users U ON U.EmpNumber = O.EmployeeId
    INNER JOIN Division D ON O.DivisionId = D.DevsionId
    INNER JOIN Locations L ON O.Location = L.LocId
    WHERE O.EmployeeId = '$employeeId' AND YEAR(O.DateCreated) = $currentYear AND MONTH(O.DateCreated) = $currentMonth
    ORDER BY O.OrderDate DESC";
    if ($result = mysqli_query($con, $query)) {
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
            $OrderedBy = $rows['OrderedBy'];
            $orderByQuery = "SELECT UserId,Fname,Lname,EmpNumber FROM `Users` WHERE UserId = '$OrderedBy' LIMIT;";
            if ($ordResult = mysqli_query($con, $orderByQuery)) {
                $j = 0;
                while ($ordRow = mysqli_fetch_assoc($ordResult)) {
                    $data[$i]['OrderedBy'][$j]['UserId'] = $ordRow['UserId'];
                    $data[$i]['OrderedBy'][$j]['Fname'] = $ordRow['Fname'];
                    $data[$i]['OrderedBy'][$j]['Lname'] = $ordRow['Lname'];
                    $data[$i]['OrderedBy'][$j]['EmpNumber'] = $ordRow['EmpNumber'];
                }
            }
            $data[$i]['OrderId'] = $rows['OrderId'];
            $data[$i]['EmployeeId'] = $rows['EmployeeId'];
            $data[$i]['OrderDate'] = $rows['OrderDate'];
            $data[$i]['Amount'] = $rows['Amount'];
            $data[$i]['IsVeg'] = $rows['IsVeg'];
            $data[$i]['OrderType'] = $rows['OrderType'];
            $data[$i]['DivisionId'] = $rows['DivisionId'];
            $data[$i]['DevisionName'] = $rows['DevisionName'];
            $data[$i]['LocId'] = $rows['Location'];
            $data[$i]['LocationName'] = $rows['LocationName'];
            $data[$i]['Status'] = $rows['OrderStatus'];
            $data[$i]['DateCreated'] = $rows['DateCreated'];
            $data[$i]['DateModified'] = $rows['DateModified'];
            $data[$i]['UserId'] = $rows['UserId'];
            $data[$i]['Fname'] = $rows['Fname'];
            $data[$i]['Lname'] = $rows['Lname'];
            $data[$i]['Phone'] = $rows['Phone'];
            $data[$i]['EmpNumber'] = $rows['EmpNumber'];
            $data[$i]['DevisionId'] = $rows['DevisionId'];
            $data[$i]['Email'] = $rows['Email'];
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
