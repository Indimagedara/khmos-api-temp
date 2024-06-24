<?php
//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require '../../../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET["id"];
    $query = "SELECT a.id, a.user_id, a.section_id, a.action, a.action_time, a.new_query, 
                     u.UserId, u.Fname, u.Lname, u.Phone, u.EmpNumber, u.DevisionId, u.Email, 
                     u.UserGroup, u.Status, u.IsPasswordChanged, u.IsAdmin, u.IsDeleted, 
                     u.DateCreated, u.Role
              FROM audit_trail a
              LEFT JOIN Users u ON a.user_id = u.UserId
              WHERE u.UserId = '$id'
              ORDER BY a.id DESC";

    $result = mysqli_query($con, $query);

    if ($result) {
        $audit_trails = [];
        while ($rows = mysqli_fetch_assoc($result)) {
            $audit_trail = [
                'id' => $rows['id'],
                'user_id' => $rows['user_id'],
                'section_id' => $rows['section_id'],
                'action' => $rows['action'],
                'action_time' => $rows['action_time'],
                'new_query' => $rows['new_query'],
                'user' => [
                    'UserId' => $rows['UserId'],
                    'Fname' => $rows['Fname'],
                    'Lname' => $rows['Lname'],
                    'Phone' => $rows['Phone'],
                    'EmpNumber' => $rows['EmpNumber'],
                    'DevisionId' => $rows['DevisionId'],
                    'Email' => $rows['Email'],
                    'UserGroup' => $rows['UserGroup'],
                    'Status' => $rows['Status'],
                    'IsPasswordChanged' => $rows['IsPasswordChanged'],
                    'IsAdmin' => $rows['IsAdmin'],
                    'IsDeleted' => $rows['IsDeleted'],
                    'DateCreated' => $rows['DateCreated'],
                    'Role' => $rows['Role']
                ]
            ];

            $audit_trails[] = $audit_trail;
        }

        echo json_encode([
            'status' => 1,
            'count' => mysqli_num_rows($result),
            'data' => $audit_trails
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Failed to fetch audit trails.'
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
