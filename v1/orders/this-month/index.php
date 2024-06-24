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
// require '../../../services/order-services.php';
$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $employeeId = $_GET['id'];
    $currentYear = date("Y");
    $currentMonth = date("m");
    $currentDate = date("d");
    $fromYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
    $toYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;
    $fromMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;

    if ($currentDate >= 15) {
        $fromDate = $currentYear . '-' . $currentMonth . '-16';
        $toDate = $toYear . '-' . $nextMonth . '-15';
    } else {
        $fromDate = $fromYear . '-' . $fromMonth . '-16';
        $toDate = $toYear . '-' . $currentMonth . '-15';
    }

    // Define the discounted and exceeded prices
    $discountedPrices = [
        '1' => 140,
        '2' => 190,
        '3' => 150
    ];

    $exceededPrices = [
        '1' => 280,
        '2' => 300,
        '3' => 300
    ];

    // Query to count the orders for the current month
    $orderCountQuery = "SELECT COUNT(*) as order_count FROM Orders 
                        WHERE EmployeeId = '$employeeId' 
                        AND STR_TO_DATE(OrderDate, '%m/%d/%Y') BETWEEN '$fromDate' AND '$toDate'";
    
    $orderCountResult = mysqli_query($con, $orderCountQuery);
    $orderCountRow = mysqli_fetch_assoc($orderCountResult);
    $orderCountForThisMonth = $orderCountRow['order_count'];

    // Main query to get order data
    $query = "SELECT O.Status as OrderStatus, L.Location as LocationName, O.*, D.*, U.* FROM Orders O 
              LEFT JOIN Users U ON U.EmpNumber = O.EmployeeId
              LEFT JOIN Division D ON O.DivisionId = D.DevsionId
              LEFT JOIN Locations L ON O.Location = L.LocId
              WHERE O.EmployeeId = '$employeeId' AND U.Status = 1 
              AND STR_TO_DATE(OrderDate, '%m/%d/%Y') BETWEEN '$fromDate' AND '$toDate'
              ORDER BY O.OrderId ASC";

    if ($result = mysqli_query($con, $query)) {
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
            $OrderedBy = $rows['OrderedBy'];
            $orderByQuery = "SELECT UserId, Fname, Lname, EmpNumber FROM Users WHERE UserId = '$OrderedBy' LIMIT 1";
            if ($ordResult = mysqli_query($con, $orderByQuery)) {
                $j = 0;
                while ($ordRow = mysqli_fetch_assoc($ordResult)) {
                    $data[$i]['OrderedBy'][$j]['UserId'] = $ordRow['UserId'];
                    $data[$i]['OrderedBy'][$j]['Fname'] = $ordRow['Fname'];
                    $data[$i]['OrderedBy'][$j]['Lname'] = $ordRow['Lname'];
                    $data[$i]['OrderedBy'][$j]['EmpNumber'] = $ordRow['EmpNumber'];
                }
            }
            $orderId = $rows['OrderId'];
            $data[$i]['OrderId'] = $orderId;
            $data[$i]['EmployeeId'] = $rows['EmployeeId'];
            $orderType = $rows['OrderType'];
            $data[$i]['OrderType'] = $orderType;
            $isVeg = $rows['IsVeg'];
            $data[$i]['IsVeg'] = $isVeg;
            $data[$i]['OrderDate'] = $rows['OrderDate'];

            // Determine the correct amount based on whether the order index exceeds the limit
            if ($i < 40) {
                $amount = $discountedPrices[$orderType];
            } else {
                $amount = $exceededPrices[$orderType];
            }
            $data[$i]['Amount'] = $amount;

            $data[$i]['IsExceeded'] = $rows['IsExceeded'];
            $data[$i]['DivisionId'] = $rows['DivisionId'];
            $data[$i]['DevisionName'] = $rows['DevisionName'];
            $data[$i]['LocId'] = $rows['Location'];
            $data[$i]['LocationName'] = $rows['LocationName'];
            $orderStatus = $rows['OrderStatus'];
            $data[$i]['Status'] = $orderStatus;
            if ($orderStatus == 3) {
                $pickedupByQuery = "SELECT * FROM orderpickup op 
                                    LEFT JOIN Users u ON op.PickedBy = u.EmpNumber 
                                    WHERE OrderId = '$orderId'";
                if ($pickedupResult = mysqli_query($con, $pickedupByQuery)) {
                    $j = 0;
                    while ($pickedupRow = mysqli_fetch_assoc($pickedupResult)) {
                        $data[$i]['PickedUpBy'][$j]['UserId'] = $pickedupRow['UserId'];
                        $data[$i]['PickedUpBy'][$j]['Fname'] = $pickedupRow['Fname'];
                        $data[$i]['PickedUpBy'][$j]['Lname'] = $pickedupRow['Lname'];
                        $data[$i]['PickedUpBy'][$j]['EmpNumber'] = $pickedupRow['EmpNumber'];
                    }
                }
            }
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
?>
