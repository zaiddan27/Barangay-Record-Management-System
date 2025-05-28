<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function send_verification($fullname, $email, $otp)
{


    $mail = new PHPMailer(true);                              // Passing true enables exceptions
    try {
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'jeromeancheta10@gmail.com'; // SMTP username
        $mail->Password = 'shzf zcpn qpdk dzfx';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, ssl also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        // SSL Options for local testing
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ];

        //Recipients
        $mail->setFrom('jeromeancheta10@gmail.com', 'Barangay Camaya');
        $mail->addAddress($email);     // Add a recipient
        //Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = "Barangay OTP Verification";
        $mail->Body = '
    <div style="max-width:500px;margin:30px auto;padding:30px 25px;background:#f8f9fa;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);font-family:sans-serif;">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="font-size:48px;line-height:1.2;">&#x1F512;</div>
            <h2 style="margin:0;color:#0d6efd;">Barangay Camaya</h2>
            <p style="margin:0;font-size:15px;color:#555;">Community Account Verification</p>
        </div>
        <hr style="margin:20px 0;">
        <p style="font-size:16px;color:#222;">Hello <b>' . $fullname . '</b>,</p>
        <p style="font-size:15px;color:#333;">
            Thank you for registering with <b>Barangay Camaya</b>!<br>
            To complete your registration, please use the verification code below:
        </p>
        <div style="text-align:center;margin:30px 0;">
            <div style="font-size:15px;color:#888;margin-bottom:8px;">Your One-Time Password (OTP):</div>
            <span style="
                display:inline-block;
                font-size:2.5rem;
                letter-spacing:12px;
                background:linear-gradient(90deg,#e3f0ff,#f0f4ff);
                color:#1565c0;
                padding:18px 38px;
                border-radius:12px;
                font-weight:700;
                border:2px solid #1976d2;
                box-shadow:0 2px 8px rgba(21,101,192,0.08);
                margin-bottom:8px;
                ">
                ' . $otp . '
            </span>
            <div style="font-size:13px;color:#888;margin-top:8px;">Copy and paste this code to verify your account.</div>
        </div>
        <p style="font-size:14px;color:#666;">
            If you did not request this, please ignore this email.<br>
            <b>Barangay Camaya</b> is committed to keeping your account secure.
        </p>
        <hr style="margin:20px 0;">
        <div style="text-align:center;font-size:13px;color:#aaa;">
            &copy; ' . date('Y') . ' Barangay Camaya. All rights reserved.
        </div>
    </div>
';

        $mail->send();
        return true;
    } catch (Exception $e) {
        // For debugging, you can uncomment the next line:
        // echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}
