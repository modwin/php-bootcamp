<?php

namespace WPROG2\S2_User_Data\Ex2_3_FileUpload\public;

use WPROG2\S2_User_Data\Ex2_1_UrlQueryParameters\public\HttpParamPrinter;

require_once __DIR__ . '/../../../../vendor/autoload.php';
header("Content-Type: text/plain; charset=utf-8");
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);

class FileHandler
{
    private const MAX_FILE_SIZE = 1024 * 1024; // 1MB
    private const DISPLAYABLE_TYPES = ['image/jpeg', 'image/png', 'text/plain', 'image/gif'];

    public static function process(array $file_data): void
    {
        if (!self::isValidFile($file_data)) {
            return;
        }

        $temp_path = $file_data['tmp_name'];
        $mime_type = mime_content_type($temp_path);

        if (in_array($mime_type, self::DISPLAYABLE_TYPES, true)) {
            header("Content-Type: $mime_type; charset=utf-8");
            readfile($temp_path);
        } else {
            header("Content-Type: text/plain; charset=utf-8");
            $file_name = basename($file_data['name']);
            $file_size = $file_data['size'];

            echo "File name: $file_name\n";
            echo "MIME type: $mime_type\n";
            echo "File size: $file_size bytes\n";
        }
    }

    private static function hasUploadError(array $file_data): bool
    {
        return !isset($file_data) || $file_data['error'] !== UPLOAD_ERR_OK;
    }

    private static function isExceedingSizeLimit(int $file_size): bool
    {
        return $file_size > self::MAX_FILE_SIZE;
    }

    private static function isValidFile(array $file_data): bool
    {
        if (self::hasUploadError($file_data)) {
            http_response_code(400);
            header('Content-Type: text/plain; charset=utf-8');
            echo "Error: No file uploaded or an error occurred while uploading.";
            return false;
        }

        $file_size = $file_data['size'];

        if (self::isExceedingSizeLimit($file_size)) {
            $status_code = http_response_code(404);
            echo $status_code;
            header('Content-Type: text/plain; charset=utf-8');
            echo "Error: File size exceeds the maximum allowed 1MB limit.";
            return false;
        }

        return true;
    }
}
