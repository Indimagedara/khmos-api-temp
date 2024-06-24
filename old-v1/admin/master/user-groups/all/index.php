<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM UserGroups";

    $result = mysqli_query($con, $query);

    if ($result) {
        $usergroups = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $usergroups[] = $row;
        }

        echo json_encode([
            'status' => 1,
            'data' => $usergroups
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Failed to fetch usergroups.'
        ]);
        http_response_code(500);
    }
} else {
    echo json_encode([
        'status' => -1,
        'message' => 'Invalid request method'
    ]);
    http_response_code(405);
}
?>
