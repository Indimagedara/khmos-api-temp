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
    if (!empty($data->DivisionId) && !empty($data->Division)) {
        $divisionId = $data->DivisionId;
        $division = $data->Division;

        // Update data in the Locations table
        $query = "UPDATE Division SET DevisionName = '$division' WHERE DevsionId = $divisionId";

        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) > 0) {
                echo json_encode([
                    'status' => 1,
                    'message' => 'Division updated successfully.'
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'No changes made. Division not found.'
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to update division.'
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
