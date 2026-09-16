<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "beefarm";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("DB connection failed: " . $conn->connect_error); }

// For now, fetch all orders (later you can filter by buyer login/session)
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = $row['id'];

    // Get items
    $itemsRes = $conn->query("SELECT product_name, quantity FROM order_items WHERE order_id=$orderId");
    $items = [];
    while ($i = $itemsRes->fetch_assoc()) {
        $items[] = $i['product_name'] . " (x" . $i['quantity'] . ")";
    }

    $orders[] = [
        "id" => $row['id'],
        "items" => implode(", ", $items),
        "grand_total" => $row['grand_total'],
        "status" => $row['status'],
        "created_at" => $row['created_at']
    ];
}

echo json_encode($orders);
$conn->close();
?>