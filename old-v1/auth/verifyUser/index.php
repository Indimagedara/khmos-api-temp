<?php
//include headers
//header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');

// including files
include_once __DIR__ . '/../../../../config/db.php';

$db = $con;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    $new_token = bin2hex(random_bytes(20)); // token generator

    $query = "SELECT * FROM Users WHERE Email = '$data->email' AND Token = '$data->token'";
    if ($results = mysqli_query($db, $query)) {
        if ($results->num_rows == 1) {

            $user_query1 = "UPDATE Users SET  Token = '$new_token' WHERE Email = '$data->email'";
            if (mysqli_query($db, $user_query1) === TRUE) {
                while ($user_data = $results->fetch_assoc()) {

                    http_response_code(200);
                    echo json_encode([
                        'status' => 1,
                        'message' => 'You have been successfully verified.',
                        'data' => [
                            'userId' => $user_data['UserId'],
                            'email' => $user_data['Email'],
                        ],
                    ]);
                }
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 0,
                    'message' => 'We were unable to confirm your identity.',
                ]);
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
}
