<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $employeeId = isset($_GET['employeeId']) ? $_GET['employeeId'] : null;
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

    if ($employeeId !== null && $startDate !== null) {
        $formattedFirstDay = date('m/d/Y', strtotime(date("Y-m-01")));
        $formattedstartDate = date('m/d/Y', strtotime($startDate));
        $formattedendDate = ($endDate !== '') ? date('m/d/Y', strtotime($endDate)) : null;

        $currentYear = date("Y");
        $currentMonth = date("m");
        $fromYear = $currentMonth == 1 ? $currentYear - 1: $currentYear;
        $toYear = $currentYear;
        $fromMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        
        $fromDate = $fromYear.'-'.$fromMonth.'-16';
        $toDate = $toYear.'-'.$currentMonth.'-15';

        $query = "SELECT o.OrderId, o.EmployeeId, o.OrderDate, o.Amount, o.IsVeg, o.OrderType, o.DivisionId, o.Location, o.OrderedBy, o.Status as OrderStatus, o.DateCreated, o.DateModified, t.OptionName, u.Fname, u.Lname, d.DevisionName,L.Location as LocationName FROM Orders o
                    LEFT JOIN TimeOptions t ON o.OrderType = t.TimeOptionId
                    LEFT JOIN Users u ON u.EmpNumber = o.EmployeeId
                    LEFT JOIN Division d ON o.DivisionId = d.DevsionId
                    LEFT JOIN Locations L ON o.Location = L.LocId
                    WHERE EmployeeId = $employeeId";
        if ($formattedendDate !== null) {
            $query .= " AND STR_TO_DATE(o.OrderDate, '%m/%d/%Y') BETWEEN '$startDate' AND '$endDate' ORDER BY o.OrderId DESC";
            // $query .= " AND OrderDate >= '$formattedstartDate' AND OrderDate <= '$formattedendDate' ORDER BY o.OrderDate DESC";
        }else{
            $query .= " AND STR_TO_DATE(o.OrderDate, '%m/%d/%Y') BETWEEN '$fromDate' AND '$toDate' ORDER BY o.OrderId DESC";
            // $query .= " AND OrderDate = '$formattedstartDate'";
            // $query .= " AND OrderDate >= '$formattedFirstDay' AND OrderDate <= '$formattedstartDate' ORDER BY o.OrderDate DESC";
        }

        $result = mysqli_query($con, $query);
        if ($result) {
            $orders = [];

            $i = 0;
            while ($rows = mysqli_fetch_assoc($result)) {
                $OrderedBy = $rows['OrderedBy'];
                $orderByQuery = "SELECT UserId,Fname,Lname,EmpNumber FROM `Users` WHERE UserId = '$OrderedBy' LIMIT 1;";
                if ($ordResult = mysqli_query($con, $orderByQuery)) {
                    $j = 0;
                    while ($ordRow = mysqli_fetch_assoc($ordResult)) {
                        $orders[$i]['OrderedBy'][$j]['UserId'] = $ordRow['UserId'];
                        $orders[$i]['OrderedBy'][$j]['Fname'] = $ordRow['Fname'];
                        $orders[$i]['OrderedBy'][$j]['Lname'] = $ordRow['Lname'];
                        $orders[$i]['OrderedBy'][$j]['EmpNumber'] = $ordRow['EmpNumber'];
                    }
                }
                $orderId = $rows['OrderId'];
                $orders[$i]['OrderId'] = $orderId;
                $orders[$i]['EmployeeId'] = $rows['EmployeeId'];
                $orders[$i]['OrderDate'] = $rows['OrderDate'];
                $orders[$i]['Amount'] = $rows['Amount'];
                $orders[$i]['IsExceeded'] = $rows['IsExceeded'];
                $orders[$i]['IsVeg'] = $rows['IsVeg'];
                $orders[$i]['OrderType'] = $rows['OrderType'];
                $orders[$i]['DivisionId'] = $rows['DivisionId'];
                $orders[$i]['DevisionName'] = $rows['DevisionName'];
                $orders[$i]['LocId'] = $rows['Location'];
                $orders[$i]['LocationName'] = $rows['LocationName'];
                $orderStatus = $rows['OrderStatus'];
                $orders[$i]['Status'] = $orderStatus;
                $orders[$i]['DateCreated'] = $rows['DateCreated'];
                $orders[$i]['DateModified'] = $rows['DateModified'];
                if($orderStatus == 3){
                    $pickedupByQuery = "SELECT * FROM `orderpickup` op LEFT JOIN Users u ON op.PickedBy = u.EmpNumber WHERE OrderId = '$orderId';";
                    if ($pickedupResult = mysqli_query($con, $pickedupByQuery)) {
                        $j = 0;
                        while ($pickedupRow = mysqli_fetch_assoc($pickedupResult)) {
                            $orders[$i]['PickedUpBy'][$j]['UserId'] = $pickedupRow['UserId'];
                            $orders[$i]['PickedUpBy'][$j]['Fname'] = $pickedupRow['Fname'];
                            $orders[$i]['PickedUpBy'][$j]['Lname'] = $pickedupRow['Lname'];
                            $orders[$i]['PickedUpBy'][$j]['EmpNumber'] = $pickedupRow['EmpNumber'];
                        }
                    }
                }
                // $orders[$i]['UserId'] = $rows['UserId'];
                // $orders[$i]['Fname'] = $rows['Fname'];
                // $orders[$i]['Lname'] = $rows['Lname'];
                // $orders[$i]['Phone'] = $rows['Phone'];
                // $orders[$i]['EmpNumber'] = $rows['EmpNumber'];
                // $orders[$i]['DevisionId'] = $rows['DevisionId'];
                // $orders[$i]['Email'] = $rows['Email'];
                // $orders[$i] = $rows;
                $i++;
            }

            echo json_encode([
                'status' => 1,
                'count' => mysqli_num_rows($result),
                'data' => $orders
            ]);
            http_response_code(200);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to fetch orders.',

            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'EmployeeId or startDate is missing'
        ]);
        http_response_code(400);
    }
} else {
    echo json_encode([
        'status' => -1,
        'message' => 'Invalid request method'
    ]);
    http_response_code(405);
}
?>
