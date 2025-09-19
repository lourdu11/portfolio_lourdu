<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) exit("⚠️ All fields are required.");
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) exit("❌ Invalid email.");

try {
    $conn = new mysqli("localhost", "root", "", "my_portfolio");

    $stmt = $conn->prepare("SELECT 1 FROM feedback WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows) {
        echo "⚠️ Email already used.";
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        $stmt->execute();
        echo "✅ Thank you! Your message has been sent successfully.";
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "❌ Try again later.";
}
?>
