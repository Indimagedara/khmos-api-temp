<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../config/db.php';
include '../../../../services/authorize-token.php';

$sectionId = 1;
$action = 'Update User';
$empId = getUserId();
    
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $empNum = isset($_GET['empNum']) ? $_GET['empNum'] : null;
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

    if ($empNum !== null && $startDate !== null) {
        $formattedstartDate = date('m/d/Y', strtotime($startDate));
        $formattedendDate = ($endDate !== '') ? date('m/d/Y', strtotime($endDate)) : null;

        $currentYear = date("Y");
        $currentMonth = date("m");
        $fromYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        $toYear = $currentYear;
        $fromMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;

        $fromDate = $fromYear . '-' . $fromMonth . '-16';
        $toDate = $toYear . '-' . $currentMonth . '-15';

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

        $query = "SELECT o.OrderId, o.EmployeeId, o.OrderDate, o.Amount, o.IsVeg, o.OrderType, o.DivisionId, o.Location, o.OrderedBy, o.Status, o.DateCreated, o.DateModified, t.OptionName, u.Fname, u.Lname, d.DevisionName, l.Location as LocationName 
                  FROM Orders o
                  LEFT JOIN TimeOptions t ON o.OrderType = t.TimeOptionId
                  LEFT JOIN Users u ON u.EmpNumber = o.EmployeeId
                  LEFT JOIN Division d ON o.DivisionId = d.DevsionId
                  LEFT JOIN Locations l ON o.Location = l.LocId
                  LEFT JOIN orderpickup op ON op.OrderId = o.OrderId
                  WHERE o.EmployeeId = $empNum";

        if ($formattedendDate !== null) {
            $query .= " AND STR_TO_DATE(o.OrderDate, '%m/%d/%Y') BETWEEN '$startDate' AND '$endDate' ORDER BY o.OrderId DESC";
        } else {
            $query .= " AND STR_TO_DATE(o.OrderDate, '%m/%d/%Y') BETWEEN '$startDate' AND '$toDate' ORDER BY o.OrderId DESC";
        }

        $result = mysqli_query($con, $query);
        if ($result) {
            $orders = [];
            $orderCount = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                // Determine the correct amount based on whether the order index exceeds the limit
                if ($orderCount < 50) {
                    $row['Amount'] = $discountedPrices[$row['OrderType']];
                } else {
                    $row['Amount'] = $exceededPrices[$row['OrderType']];
                }

                $orders[] = $row;
                $orderCount++;
            }
            $auditMessage = "$empId is generated the employeewise issue report.";
            $sanitizedMessage = mysqli_real_escape_string($con, trim($auditMessage));
            $qry = "INSERT INTO `audit_trail` (`user_id`, `section_id`, `action`, `new_query`) VALUES ('$empId', '$sectionId', '$action', '$sanitizedMessage')";
            if (mysqli_query($con, $qry)) {
                echo json_encode([
                    'status' => 1,
                    'count' => mysqli_num_rows($result),
                    'data' => $orders,
                    'auditStatus' => 'Audit added'
                ]);
                http_response_code(200);
            }else{
                echo json_encode([
                    'status' => 1,
                    'count' => mysqli_num_rows($result),
                    'data' => $orders,
                    'auditStatus' => 'Audit not added'
                ]);
                http_response_code(200);
            }
            // echo json_encode([
            //     'status' => 1,
            //     'count' => mysqli_num_rows($result),
            //     'data' => $orders
            // ]);
            // http_response_code(200);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to fetch orders.'
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
