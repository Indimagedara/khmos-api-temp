<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Assume OrderId, StartDate, and EndDate are provided in the query parameters
    $pickedBy = isset($_GET['pickedBy']) ? $_GET['pickedBy'] : null;
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;
    $formattedendDate = null;

    if ($pickedBy !== null && $startDate !== null) {
        // Format dates to 'YYYY-MM-DD' for MySQL
        $formattedStartDate = date('Y-m-d', strtotime($startDate));
        $formattedendDate = ($endDate !== null) ? date('Y-m-d', strtotime($endDate)) : null;

        // Query to fetch order details along with pickup information and user names
        $query = "
            SELECT 
                o.OrderId, 
                o.EmployeeId, 
                o.OrderDate, 
                o.Amount, 
                o.IsVeg, 
                o.OrderType, 
                o.DivisionId, 
                o.Location, 
                o.OrderedBy, 
                o.Status,
                p.OpId, 
                p.PickedBy, 
                p.PickedUpTime,
                u.Fname AS PickedByFirstName,
                u.Lname AS PickedByLastName,
                l.Location as LocationName
            FROM 
                Orders o
            LEFT JOIN 
                OrderPickup p ON o.OrderId = p.OrderId
            LEFT JOIN 
                Users u ON p.PickedBy = u.EmpNumber
            LEFT JOIN Locations l ON o.Location = l.LocId
            WHERE 
                p.PickedBy = $pickedBy
                AND p.PickedUpTime >= '$formattedStartDate'";
        if ($formattedendDate !== null) {
            $query .= "AND p.PickedUpTime <= '$formattedendDate'" ;
        }
        $result = mysqli_query($con, $query);

        if ($result) {
            $orders = [];

            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
            if ($orders) {
                echo json_encode([
                    'status' => 1,
                    'count' => mysqli_num_rows($result),
                    'data' => $orders
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Order not found.'
                ]);
                http_response_code(404);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to fetch order details.'
            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'OrderId or StartDate is missing'
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
