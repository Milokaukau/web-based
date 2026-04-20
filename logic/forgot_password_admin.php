<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $admin = getAdminByEmail($email);

        if ($admin) {
            $token = bin2hex(random_bytes(32));
            saveAdminResetToken($admin->id, $token);
            sendAdminPasswordResetEmail($email, $admin->name, $token);
        }
        // Always show success to prevent account enumeration
        $success = true;
    }
}

function sendAdminPasswordResetEmail($to_email, $to_name, $token) {
    $reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/pages/admin/reset_password.php?token=' . $token;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ccchloewee12@gmail.com';
        $mail->Password   = 'wvps dass cjoa btwy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('noreply@noair.my', 'NOAIR Admin');
        $mail->addAddress($to_email, $to_name);

        $mail->isHTML(true);
        $mail->Subject = 'Admin Password Reset Request - NOAIR';

        $mail->Body = "
            <h2>Admin Password Reset Request</h2>
            <p>Hi <strong>" . htmlspecialchars($to_name) . "</strong>,</p>
            <p>Click the button below to reset your admin password. This link expires in <strong>15 minutes</strong>.</p>
            <a href='{$reset_link}' style='background:#4f46e5; color:white; padding:12px 24px; text-decoration:none; border-radius:5px; display:inline-block;'>Reset My Password</a>
            <p>If the button doesn't work, copy this link: <br> {$reset_link}</p>
            <p style='color:#999;font-size:12px;'>If you did not request this, please ignore this email.</p>
        ";

        $mail->AltBody = "Hi {$to_name}, reset your admin password here: " . $reset_link;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("PHPMailer admin error: " . $mail->ErrorInfo);
        return false;
    }
}