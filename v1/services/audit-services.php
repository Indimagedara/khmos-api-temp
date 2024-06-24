<?php
include_once 'JWT.php';
use Firebase\JWT\JWT;

function jwtTokenValidate($token)
{
    $secret_key =
        '462D4A614E645267556B58703273357638792F413F4428472B4B6250655368566D597133743677397A244326452948404D635166546A576E5A72347537782141';
    try {
        $TokenVerfiy = JWT::decode($token, $secret_key, ['HS512']);
        
        if ($TokenVerfiy) {
            // Token is valid, return the decoded data
            return $TokenVerfiy->data;
        } else {
            return null;
        }
    } catch (Exception $e) {
        // Token is invalid or expired
        return null;
    }
}

// Example usage:
$token = "your_jwt_token_here";
$decodedData = jwtTokenValidate($token);
if ($decodedData) {
    // Access the decoded data
    echo "User ID: " . $decodedData->id . "<br>";
    echo "User Name: " . $decodedData->name . "<br>";
} else {
    echo "Invalid token.";
}


function saveAudit($con, $user, $section, $remarks){
    $query = "INSERT INTO sysaudit (`AuditId`, `UserId`, `Section`, `Remarks`) VALUES (null, '$user', '$section', '$remarks')";

    if (mysqli_query($con, $query)) {
        if (mysqli_affected_rows($con) == 1) {
            return true;
        }else{
            return false;
        }
    }
}

?>