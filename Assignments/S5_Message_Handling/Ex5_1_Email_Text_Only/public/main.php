<?php

namespace WPROG2\S5_Message_Handling\Ex5_1_Email_Text_Only\public;

use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $mailer = EmailFactory::createFromPost();

        if ($mailer->send()) {
            echo "E-postmeddelandet skickades!";
        }
    } catch (Exception $e) {
        echo "Fel vid sändning: " . $e->errorMessage();
    }
} else {
    readfile(__DIR__ . '/email.html');
}