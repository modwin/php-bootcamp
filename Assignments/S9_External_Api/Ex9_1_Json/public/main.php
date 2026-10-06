<?php

namespace WPROG2\Tests\Assignments\S9_External_APIs\Ex9_1_Json_APIs\public;

namespace WPROG2\S9_External_Api\Ex9_1_Json\public;

require_once __DIR__ . '/../../../../vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $apiClient = new PolisenApiClient();
    $apiEvents = $apiClient->getEvents();

    $typeSelected = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
    $locationSelected = isset($_GET['location']) ? trim((string)$_GET['location']) : '';

    $types = EventFilter::extractUnique($apiEvents, 'type');
    $locations = EventFilter::extractUnique($apiEvents, 'location');

    $filteredEvents = EventFilter::filter($apiEvents, $locationSelected, $typeSelected, $locationSelected);

    $typeOptions = '<option value="">-- Alla händelsetyper --</option>';
    foreach ($types as $type) {
        $selected = ($type == $typeSelected) ? 'selected' : '';
        $typeOptions .= sprintf('<option value="%s" %s>%s</option>', htmlspecialchars($type), $selected, htmlspecialchars($type));
    }

    $locationOptions = '<option value="">-- Alla händelsetyper --</option>';
    foreach ($locations as $location) {
        $selected = ($location == $locationSelected) ? 'selected' : '';
        $locationOptions .= sprintf('<option value="%s" %s>%s</option>', htmlspecialchars($location), $selected, htmlspecialchars($location));
    }

    $events = '';
    if (empty($filteredEvents)) {
        $events = '<p>Inga händelser hittades.</p>';
    } else {
        foreach ($filteredEvents as $event) {
            $events .= sprintf(
                '<article class="event-card">
                            <h3>%s</h3>
                            <p><strong>Typ: </strong> %s | <strong>Plats:</strong> %s</p>
                            <p><strong>Tid:</strong> %s</p>
                            <p>%s</p>
                       </article>',
                htmlspecialchars($event['name'] ?? ''),
                htmlspecialchars($event['type'] ?? ''),
                htmlspecialchars($event['location']['name'] ?? ''),
                htmlspecialchars($event['datetime'] ?? ''),
                htmlspecialchars($event['summary'] ?? '')
            );
        }
    }
    $html = file_get_contents(__DIR__ . '/example.html');
    $html = str_replace(
        ['---type-options---', '---location-options---', '---events-list---'],
        [$typeOptions, $locationOptions, $events],
        $html
    );

    echo $html;

} catch (\Throwable $e) {
    http_response_code(500);
    echo '<h1>Ett fel uppstod:</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';

}