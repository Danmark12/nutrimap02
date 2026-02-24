<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';



function sendOTP($toEmail, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'citynutritionoffice@elsalvadorcity.gov.ph';   // 👉 your Gmail
        $mail->Password   = 'ycth coxd gjhz vgwf';           // 👉 Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('citynutritionoffice@elsalvadorcity.gov.ph', 'CNO NutriMap');
        $mail->addAddress($toEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Your One-Time Password (OTP) for CNO NutriMap";
        $mail->Body    = "
            Hello,<br><br>
            Your One-Time Password (OTP) for CNO NutriMap login is: 
            <strong>" . htmlspecialchars($otp) . "</strong><br><br>
            This OTP is valid for 5 minutes.<br><br>
            Do not share this code with anyone.<br><br>
            Best regards,<br>
            The CNO NutriMap Team
        ";
        $mail->AltBody = "
            Hello,\n\n
            Your One-Time Password (OTP) for CNO NutriMap login is: $otp\n\n
            This OTP is valid for 5 minutes.\n\n
            Do not share this code with anyone.\n\n
            Best regards,\n
            The CNO NutriMap Team
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // In case of failure, you can log $e->getMessage()
        return false;
    }
}

/* ---------------------------------------------------------------------------
   ✅ Generic Email Sender
   --------------------------------------------------------------------------- */
function sendEmailNotification($toEmail, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'citynutritionoffice@elsalvadorcity.gov.ph';
        $mail->Password   = 'ycth coxd gjhz vgwf';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('citynutritionoffice@elsalvadorcity.gov.ph', 'CNO NutriMap');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);

        $mail->send();
        error_log("✅ Generic Email sent to $toEmail");
        return true;
    } catch (Exception $e) {
        error_log("❌ Generic Email Error: " . $mail->ErrorInfo);
        return false;
    }
}