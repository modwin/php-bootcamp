<?php

declare(strict_types=1);
namespace WPROG2\S9_External_Api\Ex9_1_Json_APIs\public;
use RuntimeException;
use JsonException;
class PolisenApiClient
{
    private string $baseUrl;

    public function __construct(string $baseUrl = 'https://polisen.se/api/events')
    {
        $this->baseUrl = $baseUrl;
    }

    public function getEvents(): array
    {
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: WPROG2-StudentApp/1.0\r\n" .
                    "Accept: application/json\r\n",
                'timeout' => 5,
            ],
        ];

        $context = stream_context_create($options);
        $json = @file_get_contents($this->baseUrl, false, $context);

        if ($json === false) {
            throw new RuntimeException('Kunde inte hämta data från Polisens API.');
        }

        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Ogiltig JSON mottogs från Polisens API.');
        }
    }
}