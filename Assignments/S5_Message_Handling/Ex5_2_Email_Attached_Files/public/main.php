<?php

namespace WPROG2\S5_Message_Handling\Ex5_2_Email_With_Attachments\public;

use PHPMailer\PHPMailer\Exception;
use WPROG2\S5_Message_Handling\Ex5_1_Email_Text_Only\public\EmailFactory;
use WPROG2\S5_Message_Handling\Ex5_2_Email_Attached_Files\public\EmailAttachmentFactory;

require_once __DIR__ . '/../../../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $mailer = EmailAttachmentFactory::createFromPost();
        $mailer = EmailAttachmentFactory::attachFiles($mailer);

        $sent = $mailer->send();

    } catch (Exception $e) {
        echo "Fel vid sändning: " . $e->errorMessage();
    }

    if ($sent) {
        echo "E-postmeddelandet skickades!";
    }

} else {
    readfile(__DIR__ . '/email.html');
}