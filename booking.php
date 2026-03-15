<?php
// ── Database config — update these to match your MySQL setup ──
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // your MySQL username
define('DB_PASS', 'abeltesfay');    // your MySQL password
define('DB_NAME', 'axumite_tours');

// ── Always respond with JSON ──────────────────────────────────
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ── Connect ───────────────────────────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'DB connection failed: ' . $conn->connect_error
    ]);
    exit;
}
$conn->set_charset('utf8mb4');

// ── Only accept POST ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Sanitize ──────────────────────────────────────────────────
function clean($val) {
    return htmlspecialchars(strip_tags(trim((string)$val)));
}

$name        = clean($_POST['name']        ?? '');
$email       = clean($_POST['email']       ?? '');
$phone       = clean($_POST['phone']       ?? '');
$destination = clean($_POST['destination'] ?? '');
$date        = clean($_POST['date']        ?? '');
$guests      = max(1, (int)($_POST['guests'] ?? 1));
$message     = clean($_POST['message']     ?? '');

// ── Validate ──────────────────────────────────────────────────
$errors = [];
if ($name    === '') $errors[] = 'Name is required.';
if ($email   === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'A valid email is required.';
if ($message === '') $errors[] = 'Message is required.';

if ($errors) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Insert — use NULLIF so empty date becomes NULL in MySQL ───
$sql = "INSERT INTO bookings (name, email, phone, destination, date, guests, message)
        VALUES (?, ?, ?, ?, NULLIF(?, ''), ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// bind: name(s) email(s) phone(s) destination(s) date(s) guests(i) message(s)
$types = 'sssssis';
$stmt->bind_param($types, $name, $email, $phone, $destination, $date, $guests, $message);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => "Booking received! We'll contact you within 24 hours."
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Execute failed: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
