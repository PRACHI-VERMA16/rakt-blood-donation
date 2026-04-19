<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
 
include '../config/db.php';
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Only POST allowed"]);
    exit;
}
 
$data = json_decode(file_get_contents("php://input"), true);
 
// Validation
$full_name  = trim($data['full_name'] ?? '');
$email      = trim($data['email'] ?? '');
$phone      = trim($data['phone'] ?? '');
$age        = intval($data['age'] ?? 0);
$blood_type = trim($data['blood_type'] ?? '');
$city       = trim($data['city'] ?? '');
 
if (!$full_name || !$email || !$phone || !$age || !$blood_type || !$city) {
    echo json_encode(["success" => false, "error" => "Sabhi fields fill karo"]);
    exit;
}
 
if ($age < 18 || $age > 65) {
    echo json_encode(["success" => false, "error" => "Age 18-65 honi chahiye"]);
    exit;
}
 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Valid email daalo"]);
    exit;
}
 
// Check if email already registered
$check = $conn->prepare("SELECT id FROM donors WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
 
if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Yeh email already registered hai"]);
    exit;
}
$check->close();
 
// Insert donor
$stmt = $conn->prepare(
    "INSERT INTO donors (full_name, email, phone, age, blood_type, city, available, created_at)
     VALUES (?, ?, ?, ?, ?, ?, 1, NOW())"
);
$stmt->bind_param("sssiss", $full_name, $email, $phone, $age, $blood_type, $city);
 
if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Registration successful! Thank you $full_name.",
        "donor_id" => $conn->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "error" => "DB error: " . $stmt->error]);
}
 
$stmt->close();
$conn->close();
?>