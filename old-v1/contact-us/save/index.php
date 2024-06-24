<?php
require __DIR__ . '/../../../config/db.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


//Get posted data
$postData = file_get_contents("php://input");

if (isset($postData) && !empty($postData)) {
    //Extract the data
    $request = json_decode($postData);


    //Validate data
    if (
        trim($request->title) === '' || trim($request->email) === '' || trim($request->firstName) === ''
        || trim($request->lastName) === '' || trim($request->phone) === '' || trim($request->message) === ''
    ) {
        http_response_code(400);
        echo json_encode([
            'status' => 0,
            'message' => 'All fields are required.',
        ]);
    } else {

        //Sanitize
        $Id = 0;
        $title = mysqli_real_escape_string($con, trim($request->title));
        $email = mysqli_real_escape_string($con, trim($request->email));
        $firstName = mysqli_real_escape_string($con, trim($request->firstName));
        $lastName = mysqli_real_escape_string($con, trim($request->lastName));
        $phone = mysqli_real_escape_string($con, trim($request->phone));
        $message = mysqli_real_escape_string($con, trim($request->message));
        $dateCreated = date("Y-m-d H:i:s");
        $lang = $data->lang;

        $responseArray = [];

        //Query
        $query = "INSERT INTO `ContactUs`(`Id`,`Title`,`FirstName`,`LastName`,`Email`,`Phone`,`Message`,`DateCreated`) VALUES ('{$Id}','{$title}','{$firstName}','{$lastName}','{$email}','{$phone}','{$message}','{$dateCreated}')";

        if ($results = mysqli_query($con, $query)) {
            $n = mysqli_affected_rows($con);
            if ($n == 1) {
                http_response_code(200);
                echo json_encode([
                    'status' => 1,
                    'message' => $lang == 'RUS' ? 'Сообщение успешно отправлено.' : 'Message sent successfully.',
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 0,
                    'message' => $lang == 'RUS' ? 'Сообщение не отправляется.' : 'Message not send.',
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 0,
                'message' => $lang == 'RUS' ? 'Сообщение не отправляется.' : 'Message not send.',
            ]);
        }
    }
}
