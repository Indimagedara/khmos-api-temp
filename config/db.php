<?php 
//Adding response headers
// header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//DB details
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Login@119');
define('DB_NAME', 'uat-khmos');


date_default_timezone_set("Asia/Colombo");

function connect(){
    $connect = mysqli_connect(DB_HOST ,DB_USER ,DB_PASS ,DB_NAME);

    if(mysqli_connect_errno()){
        die("Failed to connect: ".mysqli_connect_errno());
    }
    mysqli_set_charset($connect,"utf8");

    return $connect;
}

$con = connect();




?>