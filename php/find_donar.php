<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include '../config/db.php';

$city       = trim($_GET['city'] ?? '');
$blood_type = trim($_GET['blood_type'] ?? '');
$available  = $_GET['available'] ?? '';

$sql    = "SELECT id, full_name, phone, blood_type, city, age, available FROM donors WHERE 1=1";
$params = [];
$types  = "";

if ($city !== '') {
    $sql     .= " AND city LIKE ?";
    $city_val = "%" . $city . "%";
    $params[] = &$city_val;
    $types   .= "s";
}

if ($blood_type !== '') {
    $sql     .= " AND blood_type = ?";
    $params[] = &$blood_type;
    $types   .= "s";
}

if ($available === '1') {
    $sql .= " AND available = 1";
} elseif ($available === '0') {
    $sql .= " AND available = 0";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    array_unshift($params, $types);
    call_user_func_array([$stmt, 'bind_param'], $params);
}

$stmt->execute();
$result = $stmt->get_result();

$donors = [];
while ($row = $result->fetch_assoc()) {
    $donors[] = $row;
}

echo json_encode([
    "success" => true,
    "count"   => count($donors),
    "donors"  => $donors
]);

$stmt->close();
$conn->close();
?>