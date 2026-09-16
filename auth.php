<?php
// Response type JSON
header('Content-Type: application/json');
session_start(); // Start session to store user_id and role

// Database connection
$conn = new mysqli("localhost","root","","beefarm");

// Check connection
if($conn->connect_error){
    echo json_encode(["status"=>"error","message"=>"Database Connection Failed"]);
    exit;
}

// Get action from POST request
$action = $_POST['action'] ?? '';

/* ===================== LOGIN ===================== */
if($action == 'login'){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        $user = $res->fetch_assoc();

        if(password_verify($password, $user['password'])){
            // Save session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            echo json_encode([
                "status"=>"success",
                "message"=>"Login successfully",
                "role"=>$user['role'] // send role to frontend
            ]);
        } else {
            echo json_encode(["status"=>"error","message"=>"Invalid password"]);
        }
    } else {
        echo json_encode(["status"=>"error","message"=>"User not found"]);
    }
    exit;
}

/* ===================== REGISTER ===================== */
if($action == 'register'){
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'buyer';

    date_default_timezone_set('Asia/Colombo');
    $created_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        echo json_encode(["status"=>"error","message"=>"Email already registered"]);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (full_name,email,password,role,created_at) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss",$full_name,$email,$hashed_password,$role,$created_at);

    if($stmt->execute()){
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['role'] = $role;

        echo json_encode([
            "status"=>"success",
            "message"=>"Registration successful! Please complete your profile.",
            "redirect"=>"profile.html"
        ]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Registration failed"]);
    }
    exit;
}

/* ===================== FORGOT PASSWORD ===================== */
if($action == 'forgot'){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows == 0){
        echo json_encode(["status"=>"error","message"=>"User not found"]);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $stmt->bind_param("ss",$hashed_password,$email);

    if($stmt->execute()){
        echo json_encode(["status"=>"success","message"=>"Password reset successfully"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Password reset failed"]);
    }
    exit;
}

echo json_encode(["status"=>"error","message"=>"Invalid action"]);
exit;
?>