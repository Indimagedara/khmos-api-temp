<?php
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
require '../../../config/db.php';
$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $vegOptQuery = "SELECT * FROM `VegOptions` WHERE `Status` = 1";
    if ($vegOptResult = mysqli_query($con, $vegOptQuery)) {
        $i = 0;
        while ($vegOptRows = mysqli_fetch_assoc($vegOptResult)) {
            $data['VegOptions'][$i]['VegOptionId'] = $vegOptRows['VegOptionId'];
            $data['VegOptions'][$i]['VegOption'] = $vegOptRows['VegOption'];
            $i++;
        }
    }
    $timeOptQuery = "SELECT * FROM `TimeOptions` WHERE `Status` = 1";
    if ($timeOptResult = mysqli_query($con, $timeOptQuery)) {
        $i = 0;
        while ($timeOptRows = mysqli_fetch_assoc($timeOptResult)) {
            $data['TimeOptions'][$i]['TimeOptionId'] = $timeOptRows['TimeOptionId'];
            $data['TimeOptions'][$i]['OptionName'] = $timeOptRows['OptionName'];
            $data['TimeOptions'][$i]['EditBefore'] = $timeOptRows['EditBefore'];
            $data['TimeOptions'][$i]['KitchenStartTime'] = $timeOptRows['KitchenStartTime'];
            $data['TimeOptions'][$i]['KitchenEndTime'] = $timeOptRows['KitchenEndTime'];
            $data['TimeOptions'][$i]['DispatchStartTime'] = $timeOptRows['DispatchStartTime'];
            $data['TimeOptions'][$i]['DispatchEndTime'] = $timeOptRows['DispatchEndTime'];
            $i++;
        }
    }
    $query = "SELECT * FROM `Amounts` A LEFT JOIN TimeOptions Ti ON Ti.TimeOptionId = A.TimeOptionId
    LEFT JOIN VegOptions Vo ON Vo.VegOptionId = A.VegOptionId WHERE A.Status = 1";
    if ($result = mysqli_query($con, $query)) {
        $i = 0;
        while ($rows = mysqli_fetch_assoc($result)) {
            $data['Amounts'][$i]['type'] = $rows['TimeOptionId'];
            $data['Amounts'][$i]['option'] = $rows['VegOptionId'];
            $data['Amounts'][$i]['RealAmount'] = $rows['RealAmount'];
            $data['Amounts'][$i]['EmpAmount'] = $rows['EmpAmount'];
            $data['Amounts'][$i]['AfterLimitAmount'] = $rows['AfterLimitAmount'];
            $i++;
        }
    }
    echo json_encode($data);
} else {
    echo json_encode([
        'status' => -1
    ]);
    die();
}
