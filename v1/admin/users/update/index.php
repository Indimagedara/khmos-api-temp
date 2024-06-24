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

// include '../../../../services/audit-services.php';
include '../../../../services/authorize-token.php';

$sectionId = 1;
$action = 'Update User';
$empId = getUserId();
// echo $token;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    if (!empty($data->UserId) && !empty($data->Fname) && !empty($data->EmpNumber) && !empty($data->DivisionId) && !empty($data->UserGroup)) {
        $userId = $data->UserId;
        $fname = $data->Fname;
        $lname = $data->Lname ? $data->Lname : null;
        $phone = $data->Phone ? $data->Phone : null;
        $empNumber = $data->EmpNumber;
        $devisionId = $data->DivisionId;
        $email = $data->Email ? $data->Email : null;
        $userGroup = $data->UserGroup;
        $isAdmin = $data->IsAdmin;
        $status = $data->Status;
        $role = $data->Role ? $data->Role : 0;

        $query = "UPDATE Users SET 
                    Fname = '$fname',
                    Lname = '$lname',
                    Phone = '$phone',
                    EmpNumber = '$empNumber',
                    DevisionId = '$devisionId',
                    Email = '$email',
                    UserGroup = '$userGroup',
                    IsAdmin = '$isAdmin',
                    `Status` = '$status',
                    `Role` = '$role'
                  WHERE UserId = $userId";
        if (mysqli_query($con, $query)) {
            if (mysqli_affected_rows($con) > 0) {
                $auditMessage = "Fname = '$fname',Lname = '$lname',Phone = '$phone',EmpNumber = '$empNumber',DevisionId = '$devisionId',Email = '$email',UserGroup = '$userGroup',IsAdmin = '$isAdmin',`Status` = '$status',`Role` = '$role' For UserId = $userId";
                $sanitizedMessage = mysqli_real_escape_string($con, trim($auditMessage));
                $qry = "INSERT INTO `audit_trail` (`user_id`, `section_id`, `action`, `new_query`) VALUES ('$empId', '$sectionId', '$action', '$sanitizedMessage')";
                // $audit = logAction($conn, $sectionId, $action, json_encode($data));
                if (mysqli_query($con, $qry)) {
                    echo json_encode([
                        'status' => 1,
                        'message' => 'User updated successfully.',
                        'auditStatus' => 'Audit added'
                    ]);
                    http_response_code(200);
                }else{
                    echo json_encode([
                        'status' => 1,
                        'message' => 'User updated successfully.',
                        'auditStatus' => 'Audit not added'
                    ]);
                    http_response_code(200);
                }
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'No changes detected. User information remains the same.'
                ]);
                http_response_code(200);
            }
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to update user information.'
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
