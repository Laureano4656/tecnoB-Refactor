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
// if (isset($_GET['action'])) {
//     $action = $_GET['action'];
//     require_once("./routes/".$action."Routes.php");
// } else {
//    http_response_code(400);
//     echo json_encode(array("message" => "Action not specified"));
//     exit();
// }

// _SERVER['REQUEST_URI'] contains the URI of the current request and parse_url() is used to extract the path component of the URI.
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// dirname($_SERVER['SCRIPT_NAME']) returns the directory of the current script, which is used to remove the base path from the request URI.
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
// str_replace() is used to remove the script name from the request URI, and trim() is used to remove any leading or trailing slashes.
$route = trim(str_replace($scriptName, '', $requestUri), '/');
// explode() is used to split the route into segments based on the '/' character, and the first segment is extracted as the action.
$segments = explode('/', $route);
// The first segment is used as the action, and if it is not set, null is assigned to $action.
$action = $segments[0] ?? null;


if (file_exists("./routes/".$action."Routes.php")) {
    require_once("./routes/".$action."Routes.php");
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Route not found"));
    exit();
}
?>