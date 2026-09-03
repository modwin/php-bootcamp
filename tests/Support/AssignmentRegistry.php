<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use InvalidArgumentException;

final class AssignmentRegistry
{
    /** @return array<string, array{title: string, class: class-string, provisional: bool, tier: string, services: list<string>}> */
    public static function all(): array
    {
        return [
            '1.1' => self::entry('Säker filhantering', 1, '1_1'),
            '1.2' => self::entry('Omgivningsvariabler', 1, '1_2'),
            '1.3' => self::entry('Grafikgenerering', 1, '1_3', true),
            '1.4' => self::entry('Klientstyrd omladdning', 1, '1_4', true),
            '2.1' => self::entry('Information sänd via adressfält och länkar', 2, '2_1'),
            '2.2' => self::entry('Information sänd via formulär', 2, '2_2'),
            '2.3' => self::entry('Uppladdning av fil', 2, '2_3'),
            '3.1' => self::entry('Kodseparation med konstant informationsmängd', 3, '3_1'),
            '3.2' => self::entry('Kodseparation med variabel informationsmängd', 3, '3_2'),
            '4.1' => self::entry('Information inbakad i HTML', 4, '4_1'),
            '4.2' => self::entry('Information hos klienten', 4, '4_2'),
            '4.3' => self::entry('Användning av inbyggt stöd', 4, '4_3'),
            '5.1' => self::entry('Epost-sändning utan bifogade filer', 5, '5_1', false, 'service', ['mail']),
            '5.2' => self::entry('Epost-sändning med bifogade filer', 5, '5_2', false, 'service', ['mail']),
            '5.3' => self::entry('Epost-mottagning utan bifogade filer', 5, '5_3', true, 'service', ['mail']),
            '5.4' => self::entry('Epost-mottagning med bifogade filer', 5, '5_4', true, 'service', ['mail']),
            '6.1' => self::entry('Lättviktsdatabaser', 6, '6_1'),
            '6.2' => self::entry('Relationsdatabaser och säkerhet', 6, '6_2', false, 'service', ['database']),
            '6.3' => self::entry('Relationsdatabaser och transaktioner', 6, '6_3', false, 'service', ['database']),
            '6.4' => self::entry('Relationsdatabaser och effektivitet', 6, '6_4', false, 'service', ['database']),
            '7.1' => self::entry('Publiceringssystem', 7, '7_1'),
            '7.2' => self::entry('Syndikering', 7, '7_2', true),
            '7.3' => self::entry('Kanaler', 7, '7_3'),
            '7.4' => self::entry('Sökmotor', 7, '7_4'),
            '8.1' => self::entry('HTTP-baserad autentisering med okrypterad information', 8, '8_1', true, 'external'),
            '8.2' => self::entry('HTTP-baserad autentisering med krypterad information', 8, '8_2', false, 'external'),
            '8.3' => self::entry('HTTPS-baserad konfidentialitet och serversides-autentisering', 8, '8_3', false, 'external'),
            '8.4' => self::entry('HTTPS-baserad konfidentialitet och klientsides-autentisering', 8, '8_4', false, 'external'),
            '8.5' => self::entry('HTTPS-baserade säkra kakor', 8, '8_5', false, 'external'),
            '8.6' => self::entry('HTTPS-baserad betalningshantering', 8, '8_6'),
            '9' => self::entry('Gesällprov', 9, '9', true),
        ];
    }

    /** @return array{title: string, class: class-string, provisional: bool, tier: string, services: list<string>} */
    public static function get(string $id): array
    {
        return self::all()[$id] ?? throw new InvalidArgumentException("Unknown assignment ID: {$id}");
    }

    /** @return list<string> */
    public static function ids(): array
    {
        return array_map('strval', array_keys(self::all()));
    }

    /** @return array{title: string, class: class-string, provisional: bool, tier: string, services: list<string>} */
    private static function entry(
        string $title,
        int $section,
        string $classSuffix,
        bool $provisional = false,
        string $tier = 'local',
        array $services = [],
    ): array {
        return [
            'title' => $title,
            'class' => "WPROG2\\Tests\\Assignments\\S{$section}\\Assignment{$classSuffix}Test",
            'provisional' => $provisional,
            'tier' => $tier,
            'services' => $services,
        ];
    }
}
