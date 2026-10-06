<?php

namespace WPROG2\S9_External_Api\Ex9_1_Json\public;

class EventFilter
{

    public static function extractUnique(array $apiEvents, string $field): array
    {
        $values = [];
        foreach ($apiEvents as $event) {
            if ($field === 'location_name'){
                $val = $event['location']['name'] ?? null;
            }else {
                $val = $event[$field] ?? null;
            }

            if ($val !== null && trim($val) !== ''){
                $values[] = $val;
            }
        }
        $unique = array_unique($values);
        sort($unique, SORT_STRING | SORT_FLAG_CASE);
        sort($unique, SORT_STRING | SORT_FLAG_CASE);
        return $unique;
    }

    public static function filter(array $events, ?string $selectedType, ?string $selectedLocation): array
    {
        return array_filter($events, function (array $event) use ($selectedType, $selectedLocation) {
            $matchType = empty($selectedType) || ($event['type'] ?? '') === $selectedType;
            $matchLocation = empty($selectedLocation) || ($event['location']['name'] ?? '') === $selectedLocation;

            return $matchType && $matchLocation;
        });
    }
}