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
$action = 'Update User';
$empId = getUserId();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    if (
        !empty($data->Fname) &&
        !empty($data->EmpNumber) &&
        !empty($data->DivisionId) &&
        !empty($data->UserGroup) &&
        !empty($data->Password)
    ) {
        $fname = $data->Fname;
        $lname = $data->Lname ? $data->Lname: null;
        $phone = $data->Phone ? $data->Phone : null;
        $empNumber = $data->EmpNumber;
        $divisionId = $data->DivisionId;
        $email = $data->Email ? $data->Email : null;
        $userGroup = $data->UserGroup;
        $password = $data->Password;
        $isPasswordChanged = 0;
        $dateTime = date("Y-m-d H:i:s");
        $new_password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Check if the user with the given email or employee number already exists
        $checkQuery = "SELECT UserId FROM Users WHERE EmpNumber = '$empNumber'";
        $checkResult = mysqli_query($con, $checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            echo json_encode([
                'status' => -2,
                'message' => 'User with the provided email or employee number already exists.'
            ]);
            http_response_code(200);
        } else {
            // User does not exist, proceed with the insertion
            $insertQuery = "INSERT INTO Users (`UserId`, `Fname`, `Lname`, `Phone`, `EmpNumber`, `DevisionId`, `Email`, `UserGroup`, `Password`, `Token`, `IsPasswordChanged`, `DateCreated`) VALUES (null, '$fname', '$lname', '$phone', '$empNumber', '$divisionId', '$email', '$userGroup', '$new_password_hash', '', '$isPasswordChanged', '$dateTime')";

            if (mysqli_query($con, $insertQuery)) {
                if (mysqli_affected_rows($con) == 1) {
                    $auditMessage = "(`UserId`, `Fname`, `Lname`, `Phone`, `EmpNumber`, `DevisionId`, `Email`, `UserGroup`, `Password`, `Token`, `IsPasswordChanged`, `DateCreated`) VALUES (null, '$fname', '$lname', '$phone', '$empNumber', '$divisionId', '$email', '$userGroup', '$new_password_hash', '', '$isPasswordChanged', '$dateTime')";
                    $sanitizedMessage = mysqli_real_escape_string($con, trim($auditMessage));
                    $qry = "INSERT INTO `audit_trail` (`user_id`, `section_id`, `action`, `new_query`) VALUES ('$empId', '$sectionId', '$action', '$sanitizedMessage')";
                    if (mysqli_query($con, $qry)) {
                        echo json_encode([
                            'status' => 1,
                            'message' => 'User added successfully.',
                            'auditStatus' => 'Audit added'
                        ]);
                        http_response_code(200);
                    }else{
                        echo json_encode([
                            'status' => 1,
                            'message' => 'User added successfully.',
                            'auditStatus' => 'Audit not added'
                        ]);
                        http_response_code(200);
                    }
                } else {
                    echo json_encode([
                        'status' => 0,
                        'message' => 'Could not insert user.'
                    ]);
                    http_response_code(200);
                }
            } else {
                echo json_encode([
                    'status' => 0,
                    'message' => 'Failed to insert user.'
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
} else {
    echo json_encode([
        'status' => -1,
        'message' => 'Invalid request method'
    ]);
    http_response_code(405);
}

function generateToken() {
    // You need to implement your logic to generate a unique token
    // Example: return md5(uniqid(rand(), true));
    // Note: You should use a more secure method for generating tokens in a production environment.
    return md5(uniqid(rand(), true));
}
?>
