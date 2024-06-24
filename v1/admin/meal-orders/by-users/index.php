<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $employeeId = isset($_GET['employeeId']) ? $_GET['employeeId'] : null;
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

    if ($employeeId !== null && $startDate !== null) {
        $formattedstartDate = date('m/d/Y', strtotime($startDate));
        $formattedendDate = ($endDate !== '') ? date('m/d/Y', strtotime($endDate)) : null;

        $query = "SELECT o.OrderId, o.EmployeeId, o.OrderDate, o.Amount, o.IsVeg, o.OrderType, o.DivisionId, o.Location, o.OrderedBy, o.Status, o.DateCreated, o.DateModified, t.OptionName, u.Fname, u.Lname, d.DevisionName,l.Location as LocationName FROM Orders o
                    LEFT JOIN TimeOptions t ON o.OrderType = t.TimeOptionId
                    LEFT JOIN Users u ON u.EmpNumber = o.EmployeeId
                    LEFT JOIN Division d ON o.DivisionId = d.DevsionId
                    LEFT JOIN Locations l ON o.Location = l.LocId
                    WHERE EmployeeId = $employeeId";
        if ($formattedendDate !== null) {
            $query .= " AND OrderDate >= '$formattedstartDate' AND OrderDate <= '$formattedendDate'";
        }else{
            $query .= " AND OrderDate = '$formattedstartDate'";
            
        }

        $result = mysqli_query($con, $query);
        if ($result) {
            $orders = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
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
