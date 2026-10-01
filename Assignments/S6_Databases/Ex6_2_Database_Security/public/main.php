<?php

declare(strict_types=1);

namespace WPROG2\S6_Databases\Ex6_2_Database_Security\public;

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
];
$errors = [];
$template = null;

try {
    $template = new BlockTemplateEngine(__DIR__ . '/../var/example.html');

    $dsn = getenv('GUESTBOOK_DB_DSN');
    $user = getenv('GUESTBOOK_DB_USER');
    $password = getenv('GUESTBOOK_DB_PASSWORD');

    if ($dsn === false || $dsn === '' || $user === false || $user === '' || $password === false || $password === '') {
        throw new RuntimeException('The guestbook database environment is incomplete.');
    }

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $repository = new GuestbookRepository($pdo);

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $postedValues = [];
        foreach (['name', 'email', 'homepage', 'comment'] as $field) {
            $value = $_POST[$field] ?? '';
            if (!is_string($value)) {
                $errors[] = "Fältet {$field} har ett ogiltigt format.";
                $value = '';
            }

            $postedValues[$field] = trim(strip_tags($value));
        }

        $name = $postedValues['name'];
        $email = $postedValues['email'];
        $website = $postedValues['homepage'];
        $comment = $postedValues['comment'];

        $formValues = [
            'form_name' => $name,
            'form_email' => $email,
            'form_website' => $website,
            'form_comment' => $comment,
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

        if ($errors === []) {
            $repository->addEntry(new GuestbookEntry($name, $email, $website, $comment));
            $formValues = [
                'form_name' => '',
                'form_email' => '',
                'form_website' => '',
                'form_comment' => '',
            ];
        } else {
            http_response_code(422);
        }
    }

    $entryRows = array_map(
        static function (GuestbookEntry $entry): array {
            $createdAt = $entry->getCreatedAt() ?? '';

            return [
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
    error_log($exception->getMessage());
    http_response_code(500);
    echo 'Ett oväntat fel uppstod. Försök igen senare.';
}
