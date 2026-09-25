<?php

namespace WPROG2\S5_Message_Handling\Ex5_1_Email_Text_Only\public;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailFactory
{
    /**
     * Bygger och konfigurerar en PHPMailer-instans utifrån POST-data.
     * @throws Exception
     */
    public static function createFromPost(): PHPMailer
    {
        $from = $_POST["from"] ?? "";
        $to = $_POST["to"] ?? "";
        $cc = $_POST["cc"] ?? "";
        $bcc = $_POST["bcc"] ?? "";
        $subject = $_POST["subject"] ?? "";
        $message = $_POST["message"] ?? "";

        $mailer = new PHPMailer(true);

        // Hämta SMTP-inställningar
        $host = getenv('SMTP_HOST') ?: ($_SERVER['SMTP_HOST'] ?? ($_ENV['SMTP_HOST'] ?? '127.0.0.1'));
        $port = getenv('SMTP_PORT') ?: ($_SERVER['SMTP_PORT'] ?? ($_ENV['SMTP_PORT'] ?? 1025));

        $mailer->isSMTP();
        $mailer->Host = $host;
        $mailer->Port = (int)$port;
        $mailer->SMTPAuth = false;
        $mailer->SMTPSecure = '';
        $mailer->SMTPAutoTLS = false;
        $mailer->CharSet = PHPMailer::CHARSET_UTF8;

        // Lägg till obligatorisk fotnot
        $message .= "\n\nObservera! Detta meddelande är sänt från ett formulär på Internet och avsändaren kan vara felaktig!";
        $mailer->Subject = $subject;
        $mailer->Body = $message;

        if ($from !== "") {
            $mailer->setFrom($from);
        }
        if ($to !== "") {
            $mailer->addAddress($to);
        }
        if ($cc !== "") {
            $mailer->addCC($cc);
        }
        if ($bcc !== "") {
            $mailer->addBCC($bcc);
        }

        return $mailer;
    }
}