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
    $query = "SELECT O.Status as OrderStatus,L.Location as LocationName,O.*,D.*,U.* FROM Orders O 
    LEFT JOIN Users U ON U.EmpNumber = O.EmployeeId
    LEFT JOIN Division D ON O.DivisionId = D.DevsionId
    LEFT JOIN Locations L ON O.Location = L.LocId
    WHERE O.EmployeeId = '$employeeId' ORDER BY OrderDate DESC";
    if ($result = mysqli_query($con, $query)) {
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
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
            $data[$i]['OrderedBy'] = $rows['OrderedBy'];
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
