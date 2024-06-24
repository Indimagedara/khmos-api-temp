<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT u.UserId, u.Fname, u.Lname, u.Phone, u.EmpNumber, u.DevisionId, u.Email, d.DevisionName FROM Users u
    LEFT JOIN Division d ON u.DevisionId = d.DevsionId
    WHERE IsDeleted = 0";

    $result = mysqli_query($con, $query);

    if ($result) {
        $users = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        echo json_encode([
            'status' => 1,
            'data' => $users
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Failed to fetch users.'
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
