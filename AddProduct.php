<?php
// Database connection
$servername = "localhost"; 
$username   = "root";   // your DB username
$password   = "";       // your DB password
$dbname     = "beefarm"; // change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name        = $_POST['productName'];
    $category    = $_POST['productCategory'];
    $description = $_POST['productDescription'];
    $weight      = $_POST['weight'];

    // Handle image upload
    $imagePath = "";
    if (!empty($_FILES["productImage"]["name"])) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES["productImage"]["name"]);
        move_uploaded_file($_FILES["productImage"]["tmp_name"], $imagePath);
    }

    // Insert into DB
    $sql = "INSERT INTO products (name, category, description, weight, image) 
            VALUES ('$name', '$category', '$description', '$weight', '$imagePath')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New honey product added successfully!'); window.location.href='CWManageProduct.html';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
