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
include '../../../../services/authorize-token.php';

$sectionId = 1;
$action = 'Change Password';
$empId = getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    if (!empty($data->UserId) && !empty($data->NewPassword)) {
        $userId = $data->UserId;
        $newPassword = $data->NewPassword;
        
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $query = "UPDATE Users SET Password = '$newPasswordHash', IsPasswordChanged = '0' WHERE UserId = $userId";

        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) > 0) {
                $auditMessage = "Password saved successfully for $userId ";
                $sanitizedMessage = mysqli_real_escape_string($con, trim($auditMessage));
                $qry = "INSERT INTO `audit_trail` (`user_id`, `section_id`, `action`, `new_query`) VALUES ('$empId', '$sectionId', '$action', '$sanitizedMessage')";
                if (mysqli_query($con, $qry)) {
                    echo json_encode([
                        'status' => 1,
                        'message' => 'Password changed successfully.',
                        'auditStatus' => 'Audit added'
                    ]);
                    http_response_code(200);
                }else{
                    echo json_encode([
                        'status' => 1,
                        'message' => 'Password changed successfully.',
                        'auditStatus' => 'Audit not added'
                    ]);
                    http_response_code(200);
                }
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
