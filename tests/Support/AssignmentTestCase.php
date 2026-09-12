<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class AssignmentTestCase extends TestCase
{
    protected const ASSIGNMENT_ID = '';

    /** @var array<class-string, array{config?: AssignmentConfiguration, server?: PhpServer, base_url?: string, skip?: string}> */
    private static array $contexts = [];
    private Client $client;
    /** @var list<PhpServer> */
    private array $additionalServers = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $id = static::ASSIGNMENT_ID;
        AssignmentRegistry::get($id);
        $configuration = (new AssignmentLocator(WPROG2_ROOT))->load($id);
        if ($configuration === null) {
            self::$contexts[static::class] = ['skip' => "Assignment {$id} has not been started yet."];
            return;
        }

        $context = ['config' => $configuration];
        if ($configuration->baseUrl !== null) {
            $context['base_url'] = $configuration->baseUrl;
        } else {
            $server = new PhpServer();
            $context['base_url'] = $server->start((string) $configuration->documentRoot, $configuration->environment);
            $context['server'] = $server;
        }
        self::$contexts[static::class] = $context;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $context = self::$contexts[static::class] ?? [];
        if (isset($context['skip'])) {
            self::markTestSkipped($context['skip']);
        }
        $config = $this->configuration();
        SafeStateCleaner::reset($config->exerciseDirectory, $config->statePaths);
        $this->client = new Client([
            'base_uri' => $context['base_url'],
            'http_errors' => false,
            'allow_redirects' => false,
            'cookies' => new CookieJar(),
            'timeout' => 10,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->additionalServers as $server) {
            $server->stop();
        }
        $this->additionalServers = [];
        $context = self::$contexts[static::class] ?? [];
        if (isset($context['config'])) {
            SafeStateCleaner::reset($context['config']->exerciseDirectory, $context['config']->statePaths);
        }
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        $context = self::$contexts[static::class] ?? [];
        if (isset($context['server'])) {
            $context['server']->stop();
        }
        unset(self::$contexts[static::class]);
        parent::tearDownAfterClass();
    }

    protected function configuration(): AssignmentConfiguration
    {
        return self::$contexts[static::class]['config'];
    }

    /**
     * @throws GuzzleException
     */
    protected function request(string $method, string $routeAlias = 'main', array $options = []): ResponseInterface
    {
        return $this->client->request($method, $this->configuration()->route($routeAlias), $options);
    }

    protected function client(): Client
    {
        return $this->client;
    }

    protected function assertContentType(ResponseInterface $response, string $expected): void
    {
        self::assertStringStartsWith(
            strtolower($expected),
            strtolower($response->getHeaderLine('Content-Type')),
            'Unexpected Content-Type response header.',
        );
    }

    protected function assertValidHtml(string $html): DOMDocument
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        self::assertTrue($loaded, 'Response is not parseable HTML.');
        return $document;
    }

    protected function requireFeature(string $feature): void
    {
        if (!$this->configuration()->featureEnabled($feature)) {
            self::markTestSkipped("Optional feature '{$feature}' is not enabled.");
        }
    }

    protected function sourceDirectory(): string
    {
        return $this->configuration()->exerciseDirectory;
    }

    protected function setting(string $name, mixed $default = null): mixed
    {
        return $this->configuration()->settings[$name] ?? $default;
    }

    protected function startAdditionalServer(): string
    {
        $config = $this->configuration();
        if ($config->documentRoot === null) {
            self::fail('Additional local servers require document_root configuration.');
        }
        $server = new PhpServer();
        $this->additionalServers[] = $server;
        return $server->start($config->documentRoot, $config->environment);
    }

    protected function startFixtureServer(string $documentRoot): string
    {
        $server = new PhpServer();
        $this->additionalServers[] = $server;
        return $server->start($documentRoot);
    }
}
