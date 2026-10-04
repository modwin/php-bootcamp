<?php

declare(strict_types=1);

namespace WPROG2\S6_Databases\Ex6_3_Transactional_Database\public;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;

require_once __DIR__ . '/../../../../vendor/autoload.php';

header('Content-Type: text/html; charset=UTF-8');

$formValues = [
    'form_name' => '',
    'form_email' => '',
    'form_website' => '',
    'form_comment' => '',
    'image' => ''
];
$errors = [];
$template = null;

try {
    $template = new BlockTemplateEngine(__DIR__ . '/example.html');

    $dsn = getenv('GUESTBOOK_DB_DSN');
    $user = getenv('GUESTBOOK_DB_USER');
    $password = getenv('GUESTBOOK_DB_PASSWORD');

    if ($dsn === false || $dsn === '' || $user === false || $user === '' || $password === false) {
        throw new RuntimeException('The guestbook database environment is incomplete.');
    }

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $repository = new TransactionalGuestbookRepository($pdo);

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $name = trim(strip_tags((string) ($_POST['name'] ?? '')));
        $email = trim(strip_tags((string) ($_POST['email'] ?? '')));
        $website = trim(strip_tags((string) ($_POST['website'] ?? $_POST['homepage'] ?? '')));
        $comment = trim(strip_tags((string) ($_POST['comment'] ?? '')));
        $imageData = null;

        $formValues = [
            'form_name' => $name,
            'form_email' => $email,
            'form_website' => $website,
            'form_comment' => $comment,
            'image' => $imageData
        ];

        if ($name === '') {
            $errors[] = 'Namn måste anges.';
        } elseif (mb_strlen($name, 'UTF-8') > 100) {
            $errors[] = 'Namnet får innehålla högst 100 tecken.';
        }

        if ($email === '') {
            $errors[] = 'E-postadress måste anges.';
        } elseif (mb_strlen($email, 'UTF-8') > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Ogiltig e-postadress.';
        }

        $websiteScheme = strtolower((string) parse_url($website, PHP_URL_SCHEME));
        if ($website === '') {
            $errors[] = 'Hemsida måste anges.';
        } elseif (
            mb_strlen($website, 'UTF-8') > 2048
            || filter_var($website, FILTER_VALIDATE_URL) === false
            || !in_array($websiteScheme, ['http', 'https'], true)
        ) {
            $errors[] = 'Hemsidan måste vara en giltig http- eller https-adress.';
        }

        if ($comment === '') {
            $errors[] = 'Kommentar måste anges.';
        } elseif (mb_strlen($comment, 'UTF-8') > 5000) {
            $errors[] = 'Kommentaren får innehålla högst 5000 tecken.';
        }


        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['image']['tmp_name'];
            $mimeType = mime_content_type($tmpPath);

            if (!str_starts_with((string)$mimeType, 'image/')) {
                $mimeType = $_FILES['image']['type'] ?? $mimeType;
            }

            if (str_starts_with((string)$mimeType, 'image/')) {
                $imageData = [
                    'mime_type' => $mimeType,
                    'data' => file_get_contents($tmpPath),
                ];
            } else {
                $errors[] = 'Den uppladdade filen måste vara en bild.';
            }
        }

        if ($errors === []) {
            $repository->addEntryWithImage(
                new GuestbookEntry($name, $email, $website, $comment),
                $imageData
            );
            $formValues = [
                'form_name' => '',
                'form_email' => '',
                'form_website' => '',
                'form_comment' => '',
                'image' => ''
            ];
        } else {
            http_response_code(422);
        }
    }

    $entryRows = array_map(
        static function (GuestbookEntry $entry): array {
            $createdAt = $entry->getCreatedAt() ?? '';

            return [
                'id' => (string) $entry->getId(),
                'name' => $entry->getName(),
                'email' => $entry->getEmail(),
                'website' => $entry->getWebsite(),
                'comment' => $entry->getComment(),
                'created_at' => $createdAt,
                'created_at_iso' => str_replace(' ', 'T', $createdAt),
            ];
        },
        $repository->findAll(),
    );

    $errorRows = array_map(
        static fn (string $error): array => ['error' => $error],
        $errors,
    );

    echo $template->render(
        ['errors' => $errorRows, 'entries' => $entryRows],
        $formValues,
    );
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    $message = 'Ett fel uppstod med databasen. Försök igen senare.';

    if ($template instanceof BlockTemplateEngine) {
        echo $template->render(
            ['errors' => [['error' => $message]], 'entries' => []],
            $formValues,
        );
    } else {
        echo $message;
    }
} catch (Throwable $exception) {
    echo $exception->getMessage();
    error_log($exception->getMessage());
    http_response_code(500);

    echo "\nEtt oväntat fel uppstod. Försök igen senare.";
}