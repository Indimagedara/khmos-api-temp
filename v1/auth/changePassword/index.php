<?php
//include headers
//header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    //header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}

// including files
include_once  '../../../../config/validate_token.php';
require '../../../../config/db.php';

$db = $con;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $headers = getallheaders();
    // $authHeader = $headers['Authorization'];

    $data = json_decode(file_get_contents('php://input'));
    // if jwt is not empty
    // if (!empty($authHeader)) {
    //     $jwt = explode(' ', $authHeader);

    //     if (jwtTokenValidate($jwt[1])) {
    if (
        !empty($data->userId) &&
        !empty($data->currentPassword) &&
        !empty($data->newPassword)
    ) {

        $new_password_hash = password_hash($data->newPassword, PASSWORD_BCRYPT);

        $query = "SELECT * FROM Users WHERE UserId = '$data->userId'";

        if ($results = mysqli_query($db, $query)) {
            if ($results->num_rows == 1) {
                while ($user_data = $results->fetch_assoc()) {

                    $password = $user_data['Password'];

                    if (password_verify($data->currentPassword, $password)) {

                        if (password_verify($data->newPassword, $password)) {
                            http_response_code(400);
                            echo json_encode([
                                'status' => 0,
                                'message' => 'The new password cannot be the same as the old password. Please provide a different password.',
                            ]);
                        } else {
                            $user_query1 = "UPDATE Users SET  Password = '$new_password_hash' WHERE UserId = '$data->userId'";
                            if (mysqli_query($db, $user_query1) === TRUE) {
                                http_response_code(200);
                                echo json_encode([
                                    'status' => 1,
                                    'message' => 'Password has been reset successfully.',
                                    'data' => [],
                                ]);
                            } else {
                                http_response_code(400);
                                echo json_encode([
                                    'status' => 0,
                                    'message' => 'Failed to reset password.',
                                ]);
                            }
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode([
                            'status' => 0,
                            'message' => 'Your current password does not match the password you provided. Please try again.',
                        ]);
                    }
                }
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 0,
                    'message' => 'User not found.',
                ]);
            }
        } else {
            http_response_code(201);
            $statusArr = [
                'status' => 'not logged.'
            ];
            echo json_encode($statusArr);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 0,
            'message' => 'All required fields needed.',
        ]);
    }
    // } else {
    //     http_response_code(202);
    //     echo json_encode([
    //         'status' => -1,
    //         'message' => 'Token has been expired.',
    //     ]);
    // }
    // } else {
    //     http_response_code(401);
    //     echo json_encode([
    //         'status' => 0,
    //         'message' => 'Access Denied.',
    //     ]);
    // }
}
