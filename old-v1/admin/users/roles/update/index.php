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
    // Implement logic for updating roles
    // Example: Update role details
    $data = json_decode(file_get_contents('php://input'));

    if (
        !empty($data->RoleId) &&
        !empty($data->Role) &&
        !empty($data->Status)
    ) {
        $roleId = $data->RoleId;
        $role = $data->Role;
        $status = $data->Status;

        // Update role details
        $updateRoleQuery = "UPDATE roles SET Role='$role', Status='$status', DateModified=CURRENT_TIMESTAMP WHERE RoleId='$roleId'";

        if (mysqli_query($con, $updateRoleQuery)) {
            echo json_encode([
                'status' => 1,
                'message' => 'Role updated successfully.'
            ]);
            http_response_code(200);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to update role.'
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
