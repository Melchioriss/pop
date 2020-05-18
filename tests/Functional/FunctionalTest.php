<?php

namespace PlayOrPay\Tests\Functional;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use Exception;
use Knojector\SteamAuthenticationBundle\Security\Authentication\Token\SteamUserToken;
use PlayOrPay\Domain\User\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class FunctionalTest extends WebTestCase
{
    /** @var KernelBrowser */
    protected $client;

    /** @var EntityManagerInterface|null */
    protected $em;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var FixtureCollection<object>|null */
    private $fixtures;

    /** @var Router */
    private $router;

    /**
     * @throws DBALException
     */
    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->em = self::$container->get(EntityManagerInterface::class);
        $this->tokenStorage = self::$container->get(TokenStorageInterface::class);
        $this->router = self::$container->get('router');
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }

    /**
     * @param FixtureCollection<object> $objects
     */
    private function saveFixtures(FixtureCollection $objects): void
    {
        $metadataFactory = $this->em->getMetadataFactory();
        foreach ($objects as $object) {
            try {
                $metadata = $metadataFactory->getMetadataFor(get_class($object));
                if (!$metadata->getIdentifierFieldNames()) {
                    continue;
                }
            } catch (MappingException $e) {
                continue;
            }
            $this->em->persist($object);
        }

        $this->em->flush();
    }

    /**
     * @throws DBALException
     */
    protected function cleanDatabase(): void
    {
        $connection = $this->em->getConnection();
        $connection->getConfiguration()->setSQLLogger(null);

        $connection->prepare('SET FOREIGN_KEY_CHECKS = 0;')->execute();

        foreach ($connection->getSchemaManager()->listTableNames() as $tableName) {
            $connection->prepare("DELETE FROM {$tableName};")->execute();
        }
        $connection->prepare('SET FOREIGN_KEY_CHECKS = 1;')->execute();
        $this->em->clear();
    }

    /**
     * @throws Exception
     */
    public function authorizeAsAdmin(): void
    {
        $this->authorize('admin');
    }

    /**
     * @param string $reference
     *
     * @throws Exception
     */
    protected function authorize(string $reference): void
    {
        if (!$this->fixtures) {
            throw new Exception('You must call applyFixtures before trying to authorize as somebody');
        }

        $user = $this->fixtures->get($reference);
        if (!$user instanceof User) {
            throw new Exception(sprintf('User entity was expected, got %s', get_class($user)));
        }

        $container = $this->client->getContainer();
        $session = $container->get('session');

        $token = new SteamUserToken();
        $token->__unserialize([
            'attributes'    => [],
            'authenticated' => true,
            'user'          => $user,
            'username'      => $user->getUsername(),
        ]);

        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function assertSuccessfulResponse(): void
    {
        $this->assertResponseCode(Response::HTTP_OK);
    }

    public function assertNonSuccessfulResponse(): void
    {
        $this->assertNotHavingResponseCode(Response::HTTP_OK);
    }

    public function assertResponseCode(int $code): void
    {
        $response = $this->client->getResponse();
        $this->assertSame($code, $response->getStatusCode(), $response->getContent());
    }

    public function assertResponseHavingString(string $text): void
    {
        $response = $this->client->getResponse();
        $this->assertStringContainsString($text, $response->getContent());
    }

    public function assertNotHavingResponseCode(int $code): void
    {
        $response = $this->client->getResponse();
        $this->assertNotSame($code, $response->getStatusCode(), $response->getContent());
    }

    /**
     * @param string $file
     *
     * @return FixtureCollection<object>
     */
    public function applyFixtures(string $file): FixtureCollection
    {
        $this->fixtures = FixtureCollection::fromFile($file);
        $this->saveFixtures($this->fixtures);

        return $this->fixtures;
    }

    /**
     * @param string $routeName
     * @param array<string, string|int|bool> $params
     * @param bool $shouldBeSuccessful
     *
     * @return Response
     */
    public function request(string $routeName, array $params = [], bool $shouldBeSuccessful = true): Response
    {
        $route = $this->router->getRouteCollection()->get($routeName);
        $this->client->request($route->getMethods()[0], $this->router->generate($routeName, $params), $params);

        if ($shouldBeSuccessful) {
            $this->assertSuccessfulResponse();
        }

        return $this->client->getResponse();
    }

    public function save(): void
    {
        $this->em->flush();
    }
}
