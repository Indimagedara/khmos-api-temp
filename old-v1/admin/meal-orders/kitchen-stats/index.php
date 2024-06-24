<?php
// header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    // header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}

require '../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $todayDate = date('m/d/Y');
    $tomorrow = date('m/d/Y', strtotime($todayDate . ' +1 day'));

    // Query to fetch order statistics for today
    $query = "
    SELECT 
        o.OrderType,
        o.IsVeg,
        COUNT(*) AS OrderCount,
        t.OptionName, t.EditBefore, t.KitchenStartTime, t.KitchenEndTime, t.DispatchStartTime, t.DispatchEndTime
    FROM 
        Orders o 
    LEFT JOIN TimeOptions t ON t.TimeOptionId = o.OrderType
    WHERE 
        OrderDate = '$todayDate'
            GROUP BY 
            OrderType, IsVeg
    ";
    $tQuery = "
        SELECT 
            o.OrderType,
            o.IsVeg,
            COUNT(*) AS OrderCount,
            t.OptionName, t.EditBefore, t.KitchenStartTime, t.KitchenEndTime, t.DispatchStartTime, t.DispatchEndTime
        FROM 
            Orders o 
        LEFT JOIN TimeOptions t ON t.TimeOptionId = o.OrderType
        WHERE 
            OrderDate = '$tomorrow'
        GROUP BY 
            OrderType, IsVeg
    ";
// echo $tQuery;
    $result = mysqli_query($con, $query);
    $tResult = mysqli_query($con, $tQuery);

    if ($tResult) {
        $orderTStats = [];

        while ($tRow = mysqli_fetch_assoc($tResult)) {
            $orderTStats[] = $tRow;
        }
    }
    if ($result) {
        $orderStats = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $orderStats[] = $row;
        }

        echo json_encode([
            'status' => 1,
            'data' => $orderStats,
            'tomorrow' => $orderTStats
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Failed to fetch order statistics.'
        ]);
        http_response_code(500);
    }
} else {
    echo json_encode([
        'status' => -1,
        'message' => 'Invalid request method'
    ]);
    http_response_code(405);
}
?>
