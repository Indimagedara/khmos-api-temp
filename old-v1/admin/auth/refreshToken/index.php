<?php

//include headers
//header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-type: application/json; charset=utf-8');

// including files
require '../../../../config/db.php';

// generate json web token
include_once '../../../../config/JWT.php';

use Firebase\JWT\JWT;

//objects
$db = $con;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['token'];
    // if jwt is not empty
    if (!empty($token)) {
        $secret_key =
            '462D4A614E645267556B58703273357638792F413F4428472B4B6250655368566D597133743677397A244326452948404D635166546A576E5A72347537782141';
        $token_verify = JWT::decode($token, $secret_key, ['HS512']);
        if ($token_verify) {
            $iss = $token_verify->iss;
            $iat = time();
            $nbf = $iat + 10;
            $exp = $iat + 2061;
            $aud = 'myusers';
            $user_arr_data = [
                'id' => $token_verify->data->id,
                'name' => $token_verify->data->name,
            ];

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
                'exp' => $iat + 9800,
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
                'data' => [
                    'accessToken' => $jwt,
                    'refreshToken' => $refreshToken,
                ],
                'message' => 'Token refresh successfully.',
            ]);
        } else {
            http_response_code(202);
            echo json_encode([
                'status' => -1,
                'message' => 'Token has been Expired.',
            ]);
        }
    } else {
        http_response_code(401); // not found
        echo json_encode([
            'status' => 0,
            'message' => 'Access denied.',
        ]);
    }
}
