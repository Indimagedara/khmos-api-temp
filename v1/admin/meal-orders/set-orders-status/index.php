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

require '../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    
    // Assume orders is an array of objects with OrderId and Status
    if (!empty($data->orders) && is_array($data->orders)) {
        $ordersToUpdate = $data->orders;

        // Build a comma-separated string of OrderIds
        $orderIds = implode(',', array_map(function ($order) {
            return $order->OrderId;
        }, $ordersToUpdate));

        // Build a CASE statement to update multiple rows
        $caseStatement = '';
        foreach ($ordersToUpdate as $order) {
            $caseStatement .= " WHEN OrderId = $order->OrderId THEN $order->Status";
        }

        $query = "UPDATE Orders SET Status = CASE $caseStatement END WHERE OrderId IN ($orderIds)";

        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) > 0) {
                echo json_encode([
                    'status' => 1,
                    'message' => 'Orders status updated successfully.'
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Orders not found or status remains the same.'
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to update orders status.'
            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'Invalid or missing orders array in the payload'
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
