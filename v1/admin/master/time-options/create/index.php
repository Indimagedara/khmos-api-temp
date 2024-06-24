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
    if (!empty($data->OptionName) && !empty($data->EditBefore)) {
        $optionName = $data->OptionName;
        $editBefore = $data->EditBefore;
        $status = 1;
        $dateTime = date("Y-m-d H:i:s");

        // Insert data into the TimeOptions table
        $query = "INSERT INTO TimeOptions (`TimeOptionId`, `OptionName`, `EditBefore`, `Status`, `DateCreated`, `DateModified`) VALUES (null, '$optionName', '$editBefore', '$status', '$dateTime', '$dateTime')";

        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) == 1) {
                echo json_encode([
                    'status' => 1,
                    'message' => 'Time option inserted successfully.'
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Could not insert time option.'
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to insert time option.'
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
