<?php
// Test file to diagnose booking issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Booking System Test</h1>";

// Test 1: Database Connection
echo "<h2>1. Testing Database Connection</h2>";

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'abeltesfay');
define('DB_NAME', 'axumite_tours');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Test if bookings table exists
        $result = $conn->query("SHOW TABLES LIKE 'bookings'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Bookings table exists!</p>";
            
            // Show table structure
            $result = $conn->query("DESCRIBE bookings");
            echo "<h3>Bookings Table Structure:</h3>";
            echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>";
            }
            echo "</table>";
            
            // Show recent entries
            $result = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5");
            echo "<h3>Recent Bookings (if any):</h3>";
            if ($result->num_rows > 0) {
                echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Destination</th><th>Created</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['destination']}</td><td>{$row['created_at']}</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: orange;'>⚠️ No bookings found in database</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Bookings table does not exist!</p>";
            echo "<p>You need to run the database.sql file first.</p>";
        }
    }
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

// Test 2: POST Request Simulation
echo "<h2>2. Testing POST Request Simulation</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Test the same validation as booking.php
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
    
    echo "<h3>Cleaned Data:</h3>";
    echo "<pre>";
    echo "Name: $name\n";
    echo "Email: $email\n";
    echo "Phone: $phone\n";
    echo "Destination: $destination\n";
    echo "Date: $date\n";
    echo "Guests: $guests\n";
    echo "Message: $message\n";
    echo "</pre>";
    
    // Validation
    $errors = [];
    if ($name    === '') $errors[] = 'Name is required.';
    if ($email   === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'A valid email is required.';
    if ($message === '') $errors[] = 'Message is required.';
    
    if ($errors) {
        echo "<p style='color: red;'>❌ Validation errors: " . implode(' ', $errors) . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Validation passed!</p>";
        
        // Try to insert
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $sql = "INSERT INTO bookings (name, email, phone, destination, date, guests, message)
                    VALUES (?, ?, ?, ?, NULLIF(?, ''), ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $types = 'sssssis';
            $stmt->bind_param($types, $name, $email, $phone, $destination, $date, $guests, $message);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✅ Successfully inserted into database!</p>";
                echo "<p>New booking ID: " . $conn->insert_id . "</p>";
            } else {
                echo "<p style='color: red;'>❌ Insert failed: " . $stmt->error . "</p>";
            }
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Insert exception: " . $e->getMessage() . "</p>";
        }
    }
} else {
    echo "<p>Submit the form below to test POST handling:</p>";
    ?>
    <form method="post" action="">
        <p>Name: <input type="text" name="name" value="Test User" required></p>
        <p>Email: <input type="email" name="email" value="test@example.com" required></p>
        <p>Phone: <input type="text" name="phone" value="1234567890"></p>
        <p>Destination: <input type="text" name="destination" value="Lalibela"></p>
        <p>Date: <input type="date" name="date" value="2026-12-01"></p>
        <p>Guests: <input type="number" name="guests" value="2" min="1"></p>
        <p>Message: <textarea name="message" required>I want to book a tour!</textarea></p>
        <p><input type="submit" value="Test Booking"></p>
    </form>
    <?php
}

// Test 3: PHP Environment
echo "<h2>3. PHP Environment Check</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>MySQLi Extension: " . (extension_loaded('mysqli') ? '✅ Available' : '❌ Not available') . "</p>";
echo "<p>JSON Extension: " . (extension_loaded('json') ? '✅ Available' : '❌ Not available') . "</p>";
echo "<p>Current working directory: " . getcwd() . "</p>";
echo "<p>Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</p>";
?>
