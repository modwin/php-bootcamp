<?php

namespace WPROG2\Tests\Assignments\S9_External_APIs\Ex9_1_Json_APIs\public;
use WPROG2\S9_External_APIs\Ex9_API_Supplier\public\PolisenApiClient;

require_once __DIR__ . '/../../../../vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $apiClient = new PolisenApiClient();
    $apiEvents = $apiClient->getEvents();


}catch(\Throwable $e){
    http_response_code(500);
    echo '<h1>Ett fel uppstod:</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';

}