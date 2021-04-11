<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Url;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpClient\Exception\ServerException;

class UrlTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected function getClient(): Client
    {
        return self::createClient([], ['base_uri' => 'https://localhost']);
    }

    public function testGetCollection(): void
    {
        $response = $this->getClient()->request('GET', '/api/urls');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        self::assertJsonContains([
            '@context' => '/api/contexts/Url',
            '@id' => '/api/urls',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 20,
        ]);

        self::assertCount(20, $response->toArray()['hydra:member']);
        self::assertMatchesResourceCollectionJsonSchema(Url::class);
    }

    public function testCreateUrl(): void
    {
        $response = $this->getClient()->request('POST', '/api/urls', ['json' => [
            'shortUri' => 'testpost',
            'origUrl' => 'https://www.youtube.com/',
        ]]);

        self::assertResponseIsSuccessful();

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/api/contexts/Url',
            '@type' => 'Url',
            'short_uri' => 'testpost',
            'orig_url' => 'https://www.youtube.com/',
        ]);
        self::assertRegExp('~^/api/urls/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Url::class);
    }

    public function testRedirectUrl(): void
    {
        try {
            $response = $this->getClient()->request('GET', '/test');
            $response->getContent();
        } catch (ServerException $e) {
            self::assertStringContainsString('Cannot modify header information - headers already sent', $e->getMessage());
        }
    }

    public function testIncrementVisitsOfUrl(): void
    {
        static::bootKernel([]);
        $url = static::$container->get('doctrine')->getRepository(Url::class)->findOneBy(['shortUri' => 'test']);
        self::assertEquals(0, $url->getVisits());

        // stage 1 - hit a url
        try {
            $response = $this->getClient()->request('GET', '/test');
            $response->getContent();
        } catch (ServerException $e) {
            self::assertStringContainsString('Cannot modify header information - headers already sent', $e->getMessage());
        }

        //commit current transaction
        static::$container->get('doctrine')->getConnection(static::$connection)->commit();

        $url = static::$container->get('doctrine')->getRepository(Url::class)->findOneBy(['shortUri' => 'test']);
        self::assertEquals(1, $url->getVisits());

        // stage 2 - get url visits
        $response = $this->getClient()->request('GET', sprintf('/api/urls/%d/visits', $url->getId()));

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/Url',
            '@id' => '/api/urls/' . $url->getId(),
            '@type' => 'Url',
            'visits' => $url->getVisits(),
        ]);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertRegExp('~^/api/urls/\d+$~', $response->toArray()['@id']);
        self::assertMatchesResourceItemJsonSchema(Url::class);
    }

    public function testCreateInvalidUrl(): void
    {
        $this->getClient()->request('POST', '/api/urls', ['json' => [
            'short_uri' => 'test-url',
        ]]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'shortUri: This value should not be blank.
origUrl: This value should not be blank.',
        ]);
    }

    public function testDeleteUrl(): void
    {
        static::bootKernel([]);
        $iri = $this->findIriBy(Url::class, ['shortUri' => 'test']);
        $this->getClient()->request('DELETE', $iri);

        self::assertResponseStatusCodeSame(204);
        self::assertNull(
        // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
            static::$container->get('doctrine')->getRepository(Url::class)->findOneBy(['shortUri' => 'test'])
        );
    }
}
