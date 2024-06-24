<?php
require __DIR__ .'/../vendor/autoload.php';
include './services/authorize-token.php';
require '.../config/db.php';

include_once __DIR__ . '/../v1/auth/validate-jwt/index.php';
include_once __DIR__ .
    '/../vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once __DIR__ . '/../vendor/firebase/php-jwt/src/ExpiredException.php';
include_once __DIR__ .
    '/../vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once __DIR__ . '/../vendor/firebase/php-jwt/src/JWT.php';
use Firebase\JWT\JWT;

$jwt_secret_key = '';
// $currentUserId = 0;

function testUserFunc(){
    $token = getUserId();
    return $token;
}
function getAuthorizationHeader()
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        //Nginx or fast CGI
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(
            array_map('ucwords', array_keys($requestHeaders)),
            array_values($requestHeaders)
        );
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
function getBearerToken()
{
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function getUserId(){
    $token = getBearerToken();
    $decoded = ((base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))));
    $decodedArry = json_decode($decoded, true);
    $userId = $decodedArry['data']['id'];
    return $userId;
}

// function insertLog($conn, $sectionId, $action, $query, $userId) {
//     $sql = "INSERT INTO audit_trail (user_id, section_id, action, new_query) VALUES ('$userId', '$sectionId', '$action', '$query')";
//     if (mysqli_query($conn, $query)) {

//     }

// }

// function insertLog($conn, $sectionId, $action, $query, $userId) {
//     $sql = "INSERT INTO audit_trail (user_id, section_id, action, new_query) VALUES ('$userId', '$sectionId', '$action', '$query')";
//     if (mysqli_query($conn, $query)) {

//     }

// }
// echo 'test';
function logAction($conn, $sectionId, $action, $data) {
    // return 'test';
    // return 
    $userId = getUserId();
    // $updateData = json_encode($query);
    // $updateData = json_encode(parseQuery($query));
    $jsonData = $data;
    // $updateData = json_encode($jsonData);
    // echo $updateData;
    if ($userId) {
        // insertLog($conn, $sectionId, $action, $query, $userId);
        // $sql = "INSERT INTO `audit_trail` (`user_id`, `section_id`, `action`, `new_query`) VALUES ('$userId', '$sectionId', '$action', '454325')";
        $sql = "SELECT * FROM `audit_trail` ";
        // echo $sql;
        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            // Output the error
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        return ['status' => 'error', 'message' => 'Invalid token'];
    }
}


function parseQuery($query) {
    $parsedValues = [];

    if (preg_match('/^UPDATE .* SET (.*) WHERE .*$/', $query, $matches)) {
        $setClause = $matches[1];
        $fieldAssignments = explode(',', $setClause);
        // foreach ($fieldAssignments as $assignment) {
        //     list($field, $value) = explode('=', $assignment);
        //     $field = trim($field);
        //     $value = trim($value, " '"); // Remove extra spaces and single quotes
        //     $parsedValues[$field] = $value;
        //     echo 'upd';
        // }
    } elseif (preg_match('/^INSERT INTO .* VALUES \((.*)\)$/', $query, $matches)) {
        $valuesPart = $matches[1];
        $values = explode(',', $valuesPart);
        foreach ($values as $value) {
            $parsedValues[] = trim($value, " '"); // Remove extra spaces and single quotes
        }
    }

    return $parsedValues;
}
?>
