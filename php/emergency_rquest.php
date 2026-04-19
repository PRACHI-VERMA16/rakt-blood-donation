<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../config/db.php';

// ── GET: fetch emergency requests ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $city       = trim($_GET['city'] ?? '');
    $blood_type = trim($_GET['blood_type'] ?? '');
    $urgency    = trim($_GET['urgency'] ?? '');
    $limit      = intval($_GET['limit'] ?? 6);

    $sql    = "SELECT * FROM emergency_requests WHERE 1=1";
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

    if ($urgency !== '') {
        $sql     .= " AND urgency = ?";
        $params[] = &$urgency;
        $types   .= "s";
    }

    $sql .= " ORDER BY created_at DESC LIMIT ?";
    $params[] = &$limit;
    $types   .= "i";

    $stmt = $conn->prepare($sql);

    array_unshift($params, $types);
    call_user_func_array([$stmt, 'bind_param'], $params);

    $stmt->execute();
    $result = $stmt->get_result();

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        // Add time_ago
        $created   = strtotime($row['created_at']);
        $diff      = time() - $created;
        if ($diff < 60)          $row['time_ago'] = $diff . " sec ago";
        elseif ($diff < 3600)    $row['time_ago'] = floor($diff/60) . " min ago";
        elseif ($diff < 86400)   $row['time_ago'] = floor($diff/3600) . " hr ago";
        else                     $row['time_ago'] = floor($diff/86400) . " days ago";

        $requests[] = $row;
    }

    echo json_encode([
        "success"  => true,
        "count"    => count($requests),
        "requests" => $requests
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

// ── POST: submit new emergency request ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    $blood_type    = trim($data['blood_type'] ?? '');
    $units         = intval($data['units'] ?? 0);
    $hospital      = trim($data['hospital'] ?? '');
    $city          = trim($data['city'] ?? '');
    $urgency       = trim($data['urgency'] ?? 'Moderate');
    $contact_phone = trim($data['contact_phone'] ?? '');
    $patient_name  = trim($data['patient_name'] ?? '');
    $notes         = trim($data['notes'] ?? '');

    if (!$blood_type || !$units || !$hospital || !$city || !$contact_phone) {
        echo json_encode(["success" => false, "error" => "Sabhi required fields fill karo"]);
        exit;
    }

    $valid_urgency = ['Critical', 'Urgent', 'Moderate'];
    if (!in_array($urgency, $valid_urgency)) {
        $urgency = 'Moderate';
    }

    $stmt = $conn->prepare(
        "INSERT INTO emergency_requests
         (blood_type, units_needed, hospital, city, urgency, contact_phone, patient_name, notes, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt->bind_param(
        "sissssss",
        $blood_type, $units, $hospital, $city,
        $urgency, $contact_phone, $patient_name, $notes
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success"    => true,
            "message"    => "Emergency request posted! Nearby donors ko notify kar diya gaya.",
            "request_id" => $conn->insert_id
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "DB error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(["success" => false, "error" => "Invalid request method"]);
?>