<?php
ob_start();
ob_clean();

$allowed_origin = "http://localhost:3000";
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] === $allowed_origin) {
    header("Access-Control-Allow-Origin: $allowed_origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    http_response_code(200);
    ob_end_flush();
    exit();
}


//Ask session for cooki from React
session_start();
if(isset($_SESSION['user_id'])){
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "user" => [
            "id" => $_SESSION['user_id'],
            "nom" => $_SESSION['nom'],
            "prenom" => $_SESSION['prenom'],
            "mail" => $_SESSION['mail']
        ]
    ]);
}else{
    //if session is not
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Non connected"
    ]);
}

?>