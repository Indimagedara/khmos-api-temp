<?php
include_once __DIR__ .
    '../../../../vendor/firebase/php-jwt/src/BeforeValidException.php';
include_once __DIR__ .
    '../../../../vendor/firebase/php-jwt/src/ExpiredException.php';
include_once __DIR__ .
    '../../../../vendor/firebase/php-jwt/src/SignatureInvalidException.php';
include_once __DIR__ . '../../../../vendor/firebase/php-jwt/src/JWT.php';
use Firebase\JWT\JWT;

class ValidateJWT
{
    private $secretKey = 'sA6HTfF3DVheBwpaXAgC2gDqDteYusWRn73t9AdbggtE9RAXzWhAnr8TPaSQp3CC';

    public function validateToken($token)
    {
        if (isset($token)) {
            try {
                $decoded = JWT::decode($token, $this->secretKey, ['HS256']);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }
}

?>
