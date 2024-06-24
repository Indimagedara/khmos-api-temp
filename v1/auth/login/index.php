<?php
//include headers
//header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');

// including files
include_once __DIR__ . '/../../../config/db.php';

// generate json web token
include_once '../../../config/JWT.php';

use Firebase\JWT\JWT;

$db = $con;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    $query = "SELECT * FROM Users WHERE EmpNumber = '$data->email'";
    if ($results = mysqli_query($db, $query)) {
        if ($results->num_rows == 1) {
            while ($user_data = $results->fetch_assoc()) {
                $name = $user_data['Email'];
                $password = $user_data['Password'];
                $role = $user_data['Role'];
                $status = $user_data['Status'];

                if (password_verify($data->password, $password)) {
                    // normal password, hashed password
                    $iss = $user_data['Email'];
                    $iat = time();
                    $nbf = $iat + 10;
                    $exp = $iat + 2061;
                    $aud = 'myusers';
                    $user_arr_data = [
                        'id' => $user_data['UserId'],
                        'name' => $user_data['Email'],
                        'role' => $user_data['Role']
                    ];

                    $secret_key =
                        '462D4A614E645267556B58703273357638792F413F4428472B4B6250655368566D597133743677397A244326452948404D635166546A576E5A72347537782141';
                    $payload_info = [
                        'iss' => $iss,
                        'iat' => $iat,
                        'nbf' => $nbf,
                        'exp' => $exp,
                        'aud' => $aud,
                        'data' => $user_arr_data,
                    ];
                    $refresh_payload_info = [
                        'iss' => $iss,
                        'exp' => $iat + 10800,
                        'aud' => $aud,
                        'data' => $user_arr_data,
                    ];

                    $jwt = JWT::encode($payload_info, $secret_key, 'HS512');
                    $refreshToken = JWT::encode(
                        $refresh_payload_info,
                        $secret_key,
                        'HS512'
                    );
                    http_response_code(200);
                    echo json_encode([
                        'status' => 1,
                        'message' => $status == 1 ? 'User logged in successfully.': 'Account has been deactivated. Contact Admin.',
                        'isActive' => $status,
                        'data' => [
                            'userId' => $user_data['UserId'],
                            'empNumber' => $user_data['EmpNumber'],
                            'devisionId' => $user_data['DevisionId'],
                            'fname' => $user_data['Fname'],
                            'lname' => $user_data['Lname'],
                            'accessToken' => $jwt,
                            'refreshToken' => $refreshToken,
                            'email' => $user_data['Email'],
                            'role' => $user_data['Role'],
                            'isPasswordChanged' => $user_data['IsPasswordChanged'],
                        ],
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 0,
                        'message' => 'Incorrect password.',
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
        echo json_encode([
            'status' => 0,
            'message' => 'An error occurred. Contact Codebrix (Pvt) Ltd.'
        ]);
        http_response_code(500);
    }
}
