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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $empNum = isset($_GET['empNum']) ? $_GET['empNum'] : null;
    if ($empNum !== null) {
        $query = "SELECT u.UserId, u.Fname, u.Lname, u.Phone, u.EmpNumber, u.DevisionId as DivisionId, u.Email, u.UserGroup, u.Token, u.Status, u.IsPasswordChanged, u.IsDeleted, d.DevisionName, ug.UserGroup as UserGroupName FROM Users u
        LEFT JOIN Division d ON u.DevisionId = d.DevsionId
        LEFT JOIN UserGroups ug ON ug.UgId = u.UserGroup
        WHERE u.EmpNumber = '$empNum' AND u.IsDeleted = 0";

        $result = mysqli_query($con, $query);

        if ($result) {
            $user = mysqli_fetch_assoc($result);

            if ($user) {
                echo json_encode([
                    'status' => 1,
                    'data' => $user
                ]);
                http_response_code(200);
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'User not found.',
                    'data' => ''
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to fetch user.'
            ]);
            http_response_code(500);
        }
    } else {
        echo json_encode([
            'status' => -1,
            'message' => 'empNum parameter is missing'
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
