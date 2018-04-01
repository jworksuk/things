<?php

namespace Things\Infrastructure\Ui\Web\Slim;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Things\Domain\Model\Thing\Thing;
use Things\Domain\Model\Thing\ThingId;
use Things\Domain\Model\Thing\ThingRepository;
use Things\Domain\Model\User\User;
use Things\Domain\Model\User\UserId;
use Things\Domain\Model\User\UserRepository;
use Things\Domain\Service\PasswordHashing;
use Things\Mock\Repository\InMemoryThingRepository;
use Things\Mock\Repository\InMemoryUserRepository;

class ApiTest extends TestCase
{
    const TEST_USER_EMAIL = 'test@test.com';

    const TEST_USER_PASSWORD = 'password';
    /**
     * @var Api
     */
    protected $app;


    public function setUp()
    {
        parent::setUp();

        $this->app = Api::bootstrap();

        $this->app->getContainer()[UserRepository::class] = new InMemoryUserRepository;

        $this->app->getContainer()[UserRepository::class]->save(
            new User(
                new UserId,
                self::TEST_USER_EMAIL,
                'Unit Test',
                $this->app->getContainer()[PasswordHashing::class]->calculateHash(self::TEST_USER_PASSWORD),
                new \DateTime,
                new \DateTime
            )
        );

        $this->app->getContainer()[ThingRepository::class] = new InMemoryThingRepository;
    }

    public function testBootstrap()
    {
        $this->assertInstanceOf(App::class, $this->app);
    }

    public function testCreateUser()
    {
        /** @var Response $response */
        $response = $this->getAppResponse(
            '/users',
            'POST',
            [
                'email' => 'test1@test.com',
                'password' => 12345678
            ]
        );

        $body = json_decode(strval($response->getBody()), true);

        Assert::assertArrayHasKey('data', $body);
    }

    public function testThingList()
    {
        /** @var Response $response */
        $response = $this->getAppResponse('/things');

        $body = json_decode(strval($response->getBody()), true);

        Assert::assertArrayHasKey('data', $body);
    }

    public function testThingCreate()
    {
        $thingName = 'test';
        $thingDescription  = 'Description';

        $body = $this->createThing($thingName, $thingDescription);

        Assert::assertArrayHasKey('data', $body);
        Assert::assertEquals($thingName, $body['data'][0]['name']);
        Assert::assertEquals($thingDescription, $body['data'][0]['description']);
    }

    public function testThingRead()
    {
        $thing = new Thing(
            new ThingId,
            $this->getTestUserId(),
            'Test read',
            'Test Description'
        );

        $this->app->getContainer()[ThingRepository::class]->save($thing);

        $body = json_decode(
            strval(
                $this->getAppResponse('/things/'.$thing->getThingId())->getBody()
            ),
            true
        );


        Assert::assertArrayHasKey('data', $body);
        Assert::assertEquals(strval($thing->getThingId()), $body['data'][0]['id']);
        Assert::assertEquals($thing->getName(), $body['data'][0]['name']);
        Assert::assertEquals($thing->getDescription(), $body['data'][0]['description']);
    }

    public function testThingUpdate()
    {
        $thing = new Thing(
            new ThingId,
            $this->getTestUserId(),
            'Test read',
            'Test Description'
        );

        $this->app->getContainer()[ThingRepository::class]->save($thing);

        $thingName = 'test'.rand(0, 999);
        $thingDescription  = 'description'.rand(0, 999);


        $response = $this->getAppResponse(
            '/things/'.strval($thing->getThingId()),
            'PUT',
            [
                'name' => $thingName,
                'description' => $thingDescription
            ]
        );

        $body = json_decode(strval($response->getBody()), true);

        Assert::assertArrayHasKey('data', $body);
        Assert::assertTrue($body['data'][0]['success']);
//        Assert::assertEquals($thingId, $body['data'][0]['id']);
//        Assert::assertEquals($thingName, $body['data'][0]['name']);
//        Assert::assertEquals($thingDescription, $body['data'][0]['description']);
    }

    public function testThingDelete()
    {
        $thing = new Thing(
            new ThingId,
            $this->getTestUserId(),
            'Test read',
            'Test Description'
        );

        $this->app->getContainer()[ThingRepository::class]->save($thing);

        $response = $this->getAppResponse(
            '/things/'.strval($thing->getThingId()),
            'DELETE'
        );

        $body = json_decode(strval($response->getBody()), true);

        Assert::assertArrayHasKey('data', $body);
        Assert::assertTrue($body['data'][0]['success']);
    }

    private function getAppResponse(
        string $uri,
        $method = 'GET',
        $body = [],
        $email = self::TEST_USER_EMAIL,
        $password = self::TEST_USER_PASSWORD
    ) : Response {
        $env = Environment::mock([
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
            'PHP_AUTH_USER' => $email,
            'PHP_AUTH_PW' => $password,
        ]);
        $req = Request::createFromEnvironment($env);

        if (in_array($method, ['POST', 'PUT']) && !empty($body)) {
            $req = $req->withParsedBody($body);
        }

        $this->app->getContainer()['request'] = $req;

        return clone $this->app->run(true);
    }

    /**
     * @param $thingName
     * @param $thingDescription
     * @return array
     */
    public function createThing($thingName, $thingDescription)
    {
        /** @var Response $response */
        $response = $this->getAppResponse(
            '/things',
            'POST',
            [
                'name' => $thingName,
                'description' => $thingDescription
            ]
        );

        return json_decode(strval($response->getBody()), true);
    }

    /**
     * @return UserId
     */
    protected function getTestUserId(): UserId
    {
        return $this->app->getContainer()[UserRepository::class]->findByEmail(self::TEST_USER_EMAIL)->getUserId();
    }

    private function addThingToThingRepository()
    {
    }
}
