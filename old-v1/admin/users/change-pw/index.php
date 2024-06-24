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
// require '../../../../services/audit-services.php';
// echo 'work';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    if (!empty($data->UserId) && !empty($data->NewPassword)) {
        $userId = $data->UserId;
        $staff = $data->staff;
        $newPassword = $data->NewPassword;
        
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $query = "UPDATE Users SET `Password` = '$newPasswordHash', `IsPasswordChanged` = '0' WHERE `UserId` = $userId";
        // echo $query;
        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) > 0) {
                // saveAudit($con, $staff, 'User management', 'Changed password for $userId');
                echo json_encode([
                    'status' => 1,
                    'message' => 'Password changed successfully.'
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'User not found or password remains the same.'
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to change password.'
            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'UserId or NewPassword is missing'
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
