<?php

namespace WPROG2\S5_Message_Handling\Ex5_2_Email_Attached_Files\public;

use PHPMailer\PHPMailer\PHPMailer;
use WPROG2\S5_Message_Handling\Ex5_1_Email_Text_Only\public\EmailFactory;

class EmailAttachmentFactory extends EmailFactory
{
    // Dynamisk hantering av alla filer i $_FILES
    public static function attachFiles($mailer): PHPMailer
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $fileSpec) {
                if (!isset($fileSpec['error'])) {
                    continue;
                }

                if (is_array($fileSpec['error'])) {
                    foreach ($fileSpec['error'] as $index => $error) {
                        if ($error === UPLOAD_ERR_OK) {
                            $tmpName = $fileSpec['tmp_name'][$index] ?? '';
                            $fileName = $fileSpec['name'][$index] ?? 'attachment';
                            if ($tmpName !== '' && file_exists($tmpName)) {
                                $mailer->addAttachment($tmpName, $fileName);
                            }
                        }
                    }

                } elseif ($fileSpec['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $fileSpec['tmp_name'] ?? '';
                    $fileName = $fileSpec['name'] ?? 'attachment';
                    if ($tmpName !== '' && file_exists($tmpName)) {
                        $mailer->addAttachment($tmpName, $fileName);
                    }
                }
            }
        }
        return $mailer;
    }

}