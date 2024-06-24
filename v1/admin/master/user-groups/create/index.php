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

require '../../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    // Check if all required fields are present
    if (!empty($data->UserGroup) && isset($data->MealLimit) && isset($data->IsOverride)) {
        $userGroup = $data->UserGroup;
        $mealLimit = $data->MealLimit;
        $isOverride = $data->IsOverride;
        $dateTime = date("Y-m-d H:i:s");

        // Insert data into the UserGroups table
        $query = "INSERT INTO UserGroups (`UgId`, `UserGroup`, `MealLimit`, `IsOverride`, `DateModified`) VALUES (null, '$userGroup', $mealLimit, $isOverride, '$dateTime')";

        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) == 1) {
                echo json_encode([
                    'status' => 1,
                    'message' => 'User group inserted successfully.'
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Could not insert user group.'
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to insert user group.'
            ]);
            http_response_code(500);
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
