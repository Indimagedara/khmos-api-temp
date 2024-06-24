<?php
// including files
include_once '../../../../../config/db.php';

//include headers
// header('Access-Control-Allow-Origin: *');
header(
  'Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS'
);
header(
  'Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization'
);
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'OPTIONS') {
  // header('Access-Control-Allow-Origin: *');
  header(
    'Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization'
  );
  header('HTTP/1.1 200 OK');
  die();
}

//objects
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $role_id = $_GET['role_id'];

  if (!empty($role_id)) {
    $query = "SELECT * FROM userpermission WHERE role_id='$role_id'";

    $result = mysqli_query($con, $query);

    if ($result) {
        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        echo json_encode([
            'status' => 1,
            'data' => $data
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Failed to fetch data.'
        ]);
        http_response_code(500);
    }
  } else {
    echo json_encode([
      'status' => 0,
      'message' => 'All data required',
    ]);
  }
} else {
  http_response_code(401); // not found
  echo json_encode([
    'status' => 0,
    'message' => 'Access Dined',
  ]);
}
