<?php
/**
 * Contact Form Handler
 * Process contact form submissions and save to database
 */

require_once 'includes/config.php';
require_once 'admin/includes/db.php';

// Start session for error messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// Sanitize input (compatible with PHP 8+)
$name = trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'));
$company = trim(htmlspecialchars($_POST['company'] ?? '', ENT_QUOTES, 'UTF-8'));
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = trim(htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'));
$service = trim(htmlspecialchars($_POST['service'] ?? '', ENT_QUOTES, 'UTF-8'));
$message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));

// Validate required fields
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// If errors, redirect back
if (!empty($errors)) {
    $_SESSION['contact_errors'] = $errors;
    header('Location: contact.php?error=1');
    exit;
}

// Save to database
try {
    $pdo = getDB();
    
    $stmt = $pdo->prepare("
        INSERT INTO sz_enquiries (name, email, phone, service, message, status, created_at) 
        VALUES (?, ?, ?, ?, ?, 'new', NOW())
    ");
    
    $stmt->execute([$name, $email, $phone, $service, $message]);
    
    // Try to send email notification (optional - won't fail if mail doesn't work)
    $to = CONTACT_EMAIL;
    $subject = 'New Contact Form Submission - ' . SITE_NAME;
    
    $body = "New contact form submission:\n\n";
    $body .= "Name: $name\n";
    $body .= "Company: $company\n";
    $body .= "Email: $email\n";
    $body .= "Phone: $phone\n";
    $body .= "Service: $service\n";
    $body .= "Message:\n$message\n";
    
    $headers = "From: noreply@structzone.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Try to send email but don't fail if it doesn't work
    @mail($to, $subject, $body, $headers);
    
    // Success - redirect with success message
    header('Location: contact.php?success=1');
    exit;
    
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    header('Location: contact.php?error=1');
    exit;
}
