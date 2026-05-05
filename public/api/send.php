<?php
/**
 * PHPMailer Processing Script
 * Handles POST requests from Astro Contact form.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 1. Manually load PHPMailer (No Composer)
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


// 2. Simple, zero-dependency .env loader
function loadEnv($path) {
    if (!file_exists($path)) return false;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
    return true;
}

// Check safe location first (above web root), then fallback to current dir (for your build setup)
if (!loadEnv(__DIR__ . '/../../.mail.env')) {
    loadEnv(__DIR__ . '/.mail.env');
}




// Allow from any origin (CORS) - Optional, useful if testing across domains
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Honeypot check
    if (!empty($_POST['_honey'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Spam detected."]);
        exit;
    }

    // Sanitize input
    $name = strip_tags(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject_input = strip_tags(trim($_POST["subject"]));
    $message_content = trim($_POST["message"]);

    // Simple validation
    if (empty($name) || empty($message_content) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Please complete all fields correctly."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // --- Server Settings ---
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'localhost';
        $mail->SMTPAuth   = filter_var($_ENV['SMTP_AUTH'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $mail->Username   = $_ENV['SMTP_USERNAME'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? '';
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);

        // --- Recipients ---
        $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@example.com', $_ENV['MAIL_FROM_NAME'] ?? 'Contact Form');
        $mail->addAddress($_ENV['MAIL_TO_ADDRESS'] ?? 'admin@example.com', $_ENV['MAIL_TO_NAME'] ?? 'Admin');
        $mail->addReplyTo($email, $name);


        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission: $subject_input";
        
        // Load HTML template
        $template = file_get_contents(__DIR__ . '/email_template.html');
        
        // Replace placeholders
        $email_body = str_replace(
            ['{{name}}', '{{email}}', '{{subject}}', '{{message}}'],
            [$name, $email, $subject_input, nl2br(htmlspecialchars($message_content))],
            $template
        );
        
        $mail->Body    = $email_body;
        $mail->AltBody = strip_tags(str_replace('<br />', "\n", $email_body));

        $mail->send();

        echo json_encode(["success" => true, "message" => "Thank you! Your message has been sent."]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    // Not a POST request
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method Not Allowed"]);
}
?>
