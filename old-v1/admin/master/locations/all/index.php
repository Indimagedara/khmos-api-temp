<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Select all records from the Locations table
    $query = "SELECT * FROM Locations";

    $result = mysqli_query($con, $query);

    if ($result) {
        $locations = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $locations[] = $row;
        }

        echo json_encode([
            'status' => 1,
            'data' => $locations
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Failed to fetch locations.'
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
