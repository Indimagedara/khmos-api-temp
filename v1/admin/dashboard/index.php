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
include_once '../../../config/db.php';

$data = [];
$itineraryCategories = 0;
$itineraries = 0;
$dayTours = 0;
$discovers = 0;
$bookings = 0;
$messages = 0;
$blogs = 0;
$hotels = 0;


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $icQuery = "SELECT * FROM ItineraryCategories";
    $itineraryCategories = mysqli_num_rows(mysqli_query($con, $icQuery));

    $iQuery = "SELECT * FROM Itineraries";
    $itineraries = mysqli_num_rows(mysqli_query($con, $iQuery));

    $dQuery = "SELECT * FROM DayTours";
    $dayTours = mysqli_num_rows(mysqli_query($con, $dQuery));

    $dcQuery = "SELECT * FROM DiscoverCeylon";
    $discovers = mysqli_num_rows(mysqli_query($con, $dcQuery));

    $bQuery = "SELECT * FROM CustomerTripPlan";
    $bookings = mysqli_num_rows(mysqli_query($con, $bQuery));

    $mQuery = "SELECT * FROM ContactUs";
    $messages = mysqli_num_rows(mysqli_query($con, $mQuery));

    $blQuery = "SELECT * FROM Blogs";
    $blogs = mysqli_num_rows(mysqli_query($con, $blQuery));

    $hQuery = "SELECT * FROM Accommodations";
    $hotels = mysqli_num_rows(mysqli_query($con, $hQuery));


    echo json_encode([
        'status' => 1,
        'data' => [
            'itineraryCategories' => $itineraryCategories, 'itineraries' => $itineraries, 'dayTours' => $dayTours, 'discovers' => $discovers, 'bookings' => $bookings,
            'messages' => $messages, 'blogs' => $blogs, 'hotels' => $hotels
        ],
        'message' => '',
    ]);
} else {
    echo json_encode([
        'status' => 0
    ]);
    die();
}
