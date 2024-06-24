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
include_once __DIR__ . '/../v1/auth/validate-jwt/index.php';
include_once __DIR__ .
    '/../vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once __DIR__ . '/../vendor/firebase/php-jwt/src/ExpiredException.php';
include_once __DIR__ .
    '/../vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once __DIR__ . '/../vendor/firebase/php-jwt/src/JWT.php';
use Firebase\JWT\JWT;

function isValidToken()
{
    $tokenValidationObj = new ValidateJWT();

    $accessToken = getBearerToken();

    $isValidToken = $tokenValidationObj->validateToken($accessToken);
    if ($isValidToken != 1) {
        $responseArray = [
            'message' => 'Invalid token.',
            'status' => -1,
        ];
        echo json_encode($responseArray);
        http_response_code(201);
    } else {
        return true;
    }
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
/**
 * get access token from header
 * */
function getBearerToken()
{
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}
?>
