<?php
/**
 * DEBUG MODE
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    require_once("./routes/".$action."Routes.php");
} else {
   http_response_code(400);
    echo json_encode(array("message" => "Action not specified"));
    exit();
}

?>