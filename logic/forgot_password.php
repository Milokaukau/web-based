<?php
// 1. GLOBAL SCOPE: 'use' statements must be at the very top of the file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. Load the autoloader at the top level
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . 'vendor/autoload.php';
require_once $project_root . "database/auth.php";

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    if (empty($errors)) {
        $member = getMemberByEmail($email);

        if ($member) {
            $token = bin2hex(random_bytes(32)); 
            saveResetToken($member->id, $token);
            sendPasswordResetEmail($email, $member->name, $token);
        }
        // Always set success to true to prevent "account enumeration" 
        $success = true;
    }
}

/**
 * Function to send the email
 */
function sendPasswordResetEmail($to_email, $to_name, $token) {
    $reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/pages/reset_password.php?token=' . $token;
    
    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // --- SMTP settings ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        $mail->Username   = 'ccchloewee12@gmail.com'; 
        $mail->Password   = 'wvps dass cjoa btwy'; // 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // --- Email Content ---
        $mail->setFrom('noreply@noair.my', 'NOAIR');
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - NOAIR';

        // Body
        $mail->Body = "
            <h2>Password Reset Request</h2>
            <p>Hi <strong>" . htmlspecialchars($to_name) . "</strong>,</p>
            <p>Click the button below to reset your password. This link expires in 15 minutes.</p>
            <a href='{$reset_link}' style='background:#4f46e5; color:white; padding:12px 24px; text-decoration:none; border-radius:5px; display:inline-block;'>Reset My Password</a>
            <p>If the button doesn't work, copy this link: <br> {$reset_link}</p>
        ";

        $mail->AltBody = "Hi {$to_name}, reset your password here: " . $reset_link;

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Log the error so you can see it in your XAMPP error logs
        error_log("PHPMailer error: " . $mail->ErrorInfo);
        return false;
    }
}