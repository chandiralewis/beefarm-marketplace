<?php
include 'db.php';

// hard-coded role = buyer (you can change to farmer later if needed)
$role = "buyer";

$stmt = $conn->prepare("
    SELECT id, title, message, created_at 
    FROM notifications
    WHERE target = ? OR target = 'all'
    ORDER BY created_at DESC
");
$stmt->bind_param("s", $role);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);

$stmt->close();
$conn->close();
?>

