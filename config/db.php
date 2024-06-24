<?php 
//Adding response headers
// header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//DB details
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
// define('DB_NAME', 'meal-system');
define('DB_NAME', 'kings');
// define('DB_HOST', 'codebrix-sys.xyz');
// define('DB_USER', 'cbdev');
// define('DB_PASS', 'Technix@2021');
// define('DB_NAME', 'meal-system');

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