<?php

include_once 'JWT.php';
use Firebase\JWT\JWT;

function jwtTokenValidate($token)
{
    $secret_key =
        '462D4A614E645267556B58703273357638792F413F4428472B4B6250655368566D597133743677397A244326452948404D635166546A576E5A72347537782141';
    $TokenVerfiy = JWT::decode($token, $secret_key, ['HS512']);

    if ($TokenVerfiy) {
        return true;
    } else {
        return false;
    }
}
