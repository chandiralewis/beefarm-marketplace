<?php
header('Content-Type: application/json');
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "beefarm");

if($conn->connect_error){
    echo json_encode(["status"=>"error","message"=>"Database Connection Failed"]);
    exit;
}

// Get logged-in user
$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id){
    echo json_encode(["status"=>"error","message"=>"User not logged in"]);
    exit;
}

// Fetch profile info
$stmt = $conn->prepare("SELECT u.username, u.role, p.phone, p.address, p.profile_pic 
                        FROM users u 
                        LEFT JOIN user_profile p ON u.id = p.user_id 
                        WHERE u.id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    $profile = $res->fetch_assoc();
    echo json_encode(["status"=>"success","data"=>$profile]);
} else {
    echo json_encode(["status"=>"error","message"=>"Profile not found"]);
}
?>