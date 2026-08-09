<?php
require_once __DIR__ . '/config/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
if ($message === '') $errors[] = 'Message cannot be empty.';

if ($errors) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO contact_messages (name, email, phone, subject, message, status)
         VALUES (?, ?, ?, ?, ?, "Unread")'
    );
    $stmt->execute([$name, $email, $phone ?: null, $subject ?: null, $message]);

    echo json_encode(['success' => true, 'message' => 'Thanks! Your message has been sent — we will get back to you soon.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again later.']);
}
