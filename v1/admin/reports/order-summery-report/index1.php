<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';
    $orders = [];
    $issues = [];
    if ($startDate !== null) {
        $formattedstartDate = date('m/d/Y', strtotime($startDate));
        $formattedendDate = ($endDate !== '') ? date('m/d/Y', strtotime($endDate)) : null;

        $currentYear = date("Y");
        $currentMonth = date("m");
        $fromYear = $currentMonth == 1 ? $currentYear - 1: $currentYear;
        $toYear = $currentYear;
        $fromMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        
        $fromDate = $fromYear.'-'.$fromMonth.'-16';
        $toDate = $toYear.'-'.$currentMonth.'-15';

        $query = "SELECT o.OrderId, o.EmployeeId, o.OrderDate, o.Amount, o.IsVeg, o.OrderType, o.DivisionId, o.Location, o.OrderedBy, o.Status, o.DateCreated, o.DateModified, t.OptionName, u.Fname, u.Lname, d.DevisionName,l.Location as LocationName FROM Orders o
                    LEFT JOIN TimeOptions t ON o.OrderType = t.TimeOptionId
                    LEFT JOIN Users u ON u.EmpNumber = o.EmployeeId
                    LEFT JOIN Division d ON o.DivisionId = d.DevsionId
                    LEFT JOIN Locations l ON o.Location = l.LocId";
        if ($formattedendDate !== null) {
            $query .= " AND STR_TO_DATE(o.OrderDate, '%m/%d/%Y')  BETWEEN '$startDate' AND '$endDate' ORDER BY o.OrderId DESC";
        }else{
            $query .= " AND  STR_TO_DATE(o.OrderDate, '%m/%d/%Y')  BETWEEN '$startDate' AND '$toDate' ORDER BY o.OrderId DESC";
        }
        $query2 = "SELECT o.OrderId, o.EmployeeId, o.OrderDate, o.Amount, o.IsVeg, o.OrderType, o.DivisionId, o.Location, o.OrderedBy, o.Status, o.DateCreated, o.DateModified, t.OptionName, u.Fname, u.Lname, d.DevisionName,l.Location as LocationName FROM Orders o
                    LEFT JOIN TimeOptions t ON o.OrderType = t.TimeOptionId
                    LEFT JOIN Users u ON u.EmpNumber = o.EmployeeId
                    LEFT JOIN Division d ON o.DivisionId = d.DevsionId
                    LEFT JOIN Locations l ON o.Location = l.LocId
                    LEFT JOIN orderpickup op ON op.OrderId = o.OrderId";
        if ($formattedendDate !== null) {
            $query2 .= " AND STR_TO_DATE(o.OrderDate, '%m/%d/%Y')  BETWEEN '$startDate' AND '$endDate' ORDER BY o.OrderId DESC";
        }else{
            $query2 .= " AND  STR_TO_DATE(o.OrderDate, '%m/%d/%Y')  BETWEEN '$startDate' AND '$toDate' ORDER BY o.OrderId DESC";
        }

        $result = mysqli_query($con, $query);
        $result2 = mysqli_query($con, $query2);
        if ($result && $result2) {
            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
            while ($row2 = mysqli_fetch_assoc($result2)) {
                $issues[] = $row2;
            }

            echo json_encode([
                'status' => 1,
                'count' => mysqli_num_rows($result),
                'data' => $orders,
                'issueData' => $issues
            ]);
            http_response_code(200);
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
