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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    if (
        !empty($data->Role)
    ) {
        $role = $data->Role;
        $status = 1;
        
        // Check if the role already exists
        $checkQuery = "SELECT RoleId FROM roles WHERE Role = '$role'";
        $checkResult = mysqli_query($con, $checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            echo json_encode([
                'status' => -2,
                'message' => 'Role already exists.'
            ]);
            http_response_code(200);
        } else {
            // Role does not exist, proceed with the insertion
            $insertQuery = "INSERT INTO roles (`RoleId`, `Role`, `Status`, `DateModified`) VALUES (null, '$role', '$status', CURRENT_TIMESTAMP)";

            if (mysqli_query($con, $insertQuery)) {
                if (mysqli_affected_rows($con) == 1) {
                    echo json_encode([
                        'status' => 1,
                        'message' => 'Role inserted successfully.'
                    ]);
                    http_response_code(200);
                } else {
                    echo json_encode([
                        'status' => 0,
                        'message' => 'Could not insert role.'
                    ]);
                    http_response_code(200);
                }
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Failed to insert role.'
                ]);
                http_response_code(500);
            }
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'One or more required fields are missing'
        ]);
        http_response_code(400);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Implement logic for fetching roles
    // Example: Retrieve all roles
    $getRolesQuery = "SELECT * FROM roles";
    $result = mysqli_query($con, $getRolesQuery);

    $roles = array();
    while($row = mysqli_fetch_assoc($result)) {
        $roles[] = $row;
    }

    echo json_encode($roles);
    http_response_code(200);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
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
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Implement logic for deleting roles
    // Example: Delete role by RoleId
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->RoleId)) {
        $roleId = $data->RoleId;

        // Delete role
        $deleteRoleQuery = "DELETE FROM roles WHERE RoleId='$roleId'";

        if (mysqli_query($con, $deleteRoleQuery)) {
            echo json_encode([
                'status' => 1,
                'message' => 'Role deleted successfully.'
            ]);
            http_response_code(200);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to delete role.'
            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'RoleId is required'
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
