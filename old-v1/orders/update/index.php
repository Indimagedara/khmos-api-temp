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
require '../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    if (isset($data->EmployeeId) && isset($data->OrderDate) && isset($data->IsVeg) && isset($data->OrderType) && isset($data->OrderId) && isset($data->Location) && isset($data->Amount)) {
        $isExistQuery = "SELECT * FROM Orders WHERE OrderDate = '$data->OrderDate' AND OrderType = '$data->OrderType'";
        // echo $isExistQuery;
        $isExistResult = mysqli_query($con, $isExistQuery);

        if (mysqli_num_rows($isExistResult) != 0) {
            $orderId = $data->OrderId;
            $employeeId = $data->EmployeeId;
            $orderDate = $data->OrderDate;
            $isVeg = $data->IsVeg;
            $amount = $data->Amount;
            $orderType = $data->OrderType;
            $location = $data->Location;
    
            $query = "UPDATE Orders SET
                `OrderDate` = '$orderDate',
                `IsVeg` = '$isVeg',
                `OrderType` = '$orderType',
                `amount` = '$amount',
                `Location` = '$location'
                WHERE `OrderId` = '$orderId'";
    
            if (mysqli_query($con, $query)) {
                if (mysqli_affected_rows($con) >= 0) {
                    echo json_encode([
                        'status' => 1,
                        'message' => 'Order updated successfully.'
                    ]);
                    http_response_code(200);
                } else {
                    echo json_encode([
                        'status' => 0,
                        'message' => 'No records were updated.'
                    ]);
                    http_response_code(200);
                }
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Failed to update the order.'
                ]);
                http_response_code(500);
            }
        }else{
            echo json_encode([
                'status' => 2,
                'message' => 'The selected date and order type already exists.'
            ]);
            http_response_code(200);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'One or more required fields are missing'
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
