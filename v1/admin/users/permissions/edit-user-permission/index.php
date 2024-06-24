<?php
//inlcude headers

// header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
    exit;
}

// including files
include_once '../../../../../config/db.php';
include_once '../../../../../services/user-services.php';
include_once '../../../../../services/authorize-token.php';

$sectionId = 1;
$action = 'User Permission Update';
$empId = getUserId();

//objects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents('php://input'));
  // if (isValidToken()) {
  if (
    !empty($data->up_id) &&
    !empty($data->user_id) &&
    !empty($data->role_id) &&
    !empty($data->permissions)
  ) {
    $up_id = mysqli_real_escape_string($con, $data->up_id);
    $user_id = mysqli_real_escape_string($con, $data->user_id);
    $role_id = mysqli_real_escape_string($con, $data->role_id);
    $permission = mysqli_real_escape_string($con, $data->permissions);

    $users_query = "UPDATE `userpermission` SET  `user_id` = '{$user_id}', `role_id` = '{$role_id}', `permission` = '{$permission}' WHERE `up_id` = '{$up_id}'";

    if (mysqli_query($con, $users_query) === true) {
      $auditMessage = "user_id = '$user_id', role_id = '$role_id', permission = '$permission' WHERE up_id = '$up_id";
      $sanitizedMessage = mysqli_real_escape_string($con, trim($auditMessage));
      $qry = "INSERT INTO `audit_trail` (`user_id`, `section_id`, `action`, `new_query`) VALUES ('$empId', '$sectionId', '$action', '$sanitizedMessage')";
      if (mysqli_query($con, $qry)) {
          echo json_encode([
              'status' => 1,
              'message' => 'User permission updated successfully..',
              'auditStatus' => 'Audit added'
          ]);
          http_response_code(200);
      }else{
          echo json_encode([
              'status' => 1,
              'message' => 'User permission updated successfully..',
              'auditStatus' => 'Audit not added'
          ]);
          http_response_code(200);
      }
    } else {
      http_response_code(400);
      echo json_encode([
        'status' => 0,
        'message' => 'User permission not updated.',
      ]);
    }
  } else {
    http_response_code(500);
    echo json_encode([
      'status' => 0,
      'message' => 'All data needed',
    ]);
  }
  // }
} else {
  http_response_code(401); // not found
  echo json_encode([
    'status' => 0,
    'message' => 'Access Dined',
  ]);
}
