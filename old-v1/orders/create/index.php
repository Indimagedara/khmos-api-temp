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
include('../../services/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); 

    if (is_array($data) && !empty($data)) {
        $insertedIds = []; 
        $existCount = 0; 
        $succesCount = 0;
        foreach ($data as $row) {
            if (isset($row['orderType'], $row['userId'], $row['orderDate'])) {
                $UserId = mysqli_real_escape_string($con, $row['userId']);
                $OrderDate = mysqli_real_escape_string($con, $row['orderDate']);
                $Amount = isset($row['amount']) ? mysqli_real_escape_string($con, $row['amount']) : null;
                $IsExceeded = isset($row['isExceeded']) ? $row['isExceeded'] : false;
                $IsVeg = isset($row['isVeg']) ? mysqli_real_escape_string($con, $row['isVeg']) : null;
                $OrderType = isset($row['orderType']) ? mysqli_real_escape_string($con, $row['orderType']) : null;
                $DivisionId = isset($row['divisionId']) ? mysqli_real_escape_string($con, $row['divisionId']) : null;
                $SelectedLocation = isset($row['location']) ? mysqli_real_escape_string($con, $row['location']) : null;
                $OrderedBy = isset($row['orderBy']) ? mysqli_real_escape_string($con, $row['orderBy']) : null;
                $Status = isset($row['status']) ? mysqli_real_escape_string($con, $row['status']) : null;
                if(!isOrderExists($UserId, $OrderDate, $OrderType, $con)){
                    $query = "INSERT INTO `Orders` (`EmployeeId`, `OrderDate`, `Amount`, `IsExceeded`, `IsVeg`, `OrderType`, `DivisionId`, `Location`,`OrderedBy`, `Status`, `DateCreated`, `DateModified`) 
                    VALUES ('$UserId', '$OrderDate', '$Amount', '$IsExceeded', '$IsVeg', '$OrderType', '$DivisionId', '$SelectedLocation','$OrderedBy', '0', NOW(), NOW())";
                    // echo $query;
                    if (mysqli_query($con, $query)) {
                        $insertedIds[] = mysqli_insert_id($con);
                        $succesCount++;
                    } else {
                        echo json_encode([
                            'status' => 0,
                            'message' => 'An error occurred. Contact Codebrix (Pvt) Ltd.',
                            'error' => ':'.mysqli_error($con)
                        ]);
                        http_response_code(500);
                        exit; 
                    }
                }else{
                    $existCount++;
                }
            }
        }

        if (!empty($insertedIds) || $existCount != 0) {
            echo json_encode([
                'status' => 1,
                'existCount' => $existCount,
                'successCount' => $succesCount,
                'message' => 'Data inserted successfully.',
                'insertedIds' => $insertedIds,
            ]);
            http_response_code(200);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'No valid data rows to insert.',
            ]);
            http_response_code(400); 
        }
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Invalid data format.',
        ]);
        http_response_code(400); 
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Invalid request method.',
    ]);
    http_response_code(405); 
}
