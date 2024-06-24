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
include '../../../../services/authorize-token.php';

$sectionId = 1;
$action = 'Master data Time options';
$empId = getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    // Check if the required field is present
    if (!empty($data->TimeOptionId)) {
        $timeOptionId = $data->TimeOptionId;

        // Delete data from the TimeOptions table
        $query = "DELETE FROM TimeOptions WHERE TimeOptionId = $timeOptionId";

        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) > 0) {
                $auditMessage = "$empId is deleted the LocId = $locId";
                $sanitizedMessage = mysqli_real_escape_string($con, trim($auditMessage));
                $qry = "INSERT INTO `audit_trail` (`user_id`, `section_id`, `action`, `new_query`) VALUES ('$empId', '$sectionId', '$action', '$sanitizedMessage')";
                if (mysqli_query($con, $qry)) {
                    echo json_encode([
                        'status' => 1,
                        'message' => 'Time option deleted successfully.',
                        'auditStatus' => 'Audit added'
                    ]);
                    http_response_code(200);
                }else{
                    echo json_encode([
                        'status' => 1,
                        'message' => 'Time option deleted successfully.',
                        'auditStatus' => 'Audit not added'
                    ]);
                    http_response_code(200);
                }
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Time option not found. No changes made.'
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to delete time option.'
            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'TimeOptionId is missing'
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
