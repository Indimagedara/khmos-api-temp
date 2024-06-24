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
include_once __DIR__ . '/../../../../config/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (
        !empty($data->email) &&
        !empty($data->dateCreated) &&
        !empty($data->password)
    ) {
        $userId = 0;

        $email = $data->email;
        $token = bin2hex(random_bytes(20)); // token generator
        $dateCreated = $data->dateCreated;
        $password = $data->password;
        $password_hash = password_hash($data->password, PASSWORD_BCRYPT);

        $query = "SELECT * FROM Users WHERE Email = '$data->email'";

        if ($results = mysqli_query($con, $query)) {
            if ($results->num_rows > 0) {
                http_response_code(200);
                echo json_encode([
                    'status' => 0,
                    'message' => 'This email is already taken. Please choose another email.',
                ]);
            } else {

                $user_query = "INSERT INTO Users (UserId, Email, Password, Token,IsPasswordChanged, DateCreated) VALUES ('$userId', '$email', '$password_hash', '$token', '0', '$dateCreated')";

                if (mysqli_query($con, $user_query) === TRUE) {
                    http_response_code(200);
                    echo json_encode([
                        'status' => 1,
                        'message' => 'User has been created successfully.',
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 0,
                        'message' => 'User not created.',
                    ]);
                }
            }
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 0,
            'message' => 'All information is needed.',
        ]);
    }
} else {
    http_response_code(401);
    echo json_encode([
        'status' => 0,
        'message' => 'Access denied.',
    ]);
}
