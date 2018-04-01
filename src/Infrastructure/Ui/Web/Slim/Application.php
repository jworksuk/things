<?php

namespace Things\Infrastructure\Ui\Web\Slim;

use Bugsnag\Client;
use PDO;
use Dotenv\Dotenv;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Things\Application\DataTransformer\Response\ResponseTransformer;
use Things\Application\Exception\HttpException;
use Things\Application\Http\Controller\ThingController;
use Things\Application\Http\Controller\UserController;
use Things\Application\Http\Middleware\UserFromBasicAuthMiddleware;
use Things\Application\Service\ThingService;
use Things\Application\Service\UserService;
use Things\Domain\Model\Thing\ThingRepository;
use Things\Domain\Model\User\UserRepository;
use Things\Domain\Service\PasswordHashing;
use Things\Infrastructure\Persistence\Sql\SqlThingRepository;
use Things\Infrastructure\Persistence\Sql\SqlUserRepository;
use Things\Infrastructure\Domain\Service\Md5PasswordHashing;

/**
 * Class Application
 * @package Things\Infrastructure\Ui\Web\Slim
 */
class Application extends App
{
    /**
     * Bootstrap app.
     * @return static
     */
    public static function bootstrap()
    {
        // Load .env file
        $dotenv = new Dotenv(__DIR__.'/../../../../../');
        $dotenv->load();
        $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);

        // app config
        $config = [
            'displayErrorDetails' => true,
            'addContentLengthHeader' => false,
            'db' => [
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'pass' => getenv('DB_PASS'),
                'name' => getenv('DB_NAME'),
            ]
        ];

        $app = new static([
            "settings" => $config
        ]);

        // Container
        $container = $app->getContainer();

        // @codeCoverageIgnoreStart
        // Database
        $container['connection'] = function ($c) {
            $db = $c['settings']['db'];
            $pdo = new PDO(
                "mysql:host=". $db['host'] .";dbname=".$db['name'],
                $db['user'],
                $db['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            return $pdo;
        };

        $container[UserRepository::class] = function ($container) {
            return new SqlUserRepository($container['connection']);
        };

        $container[ThingRepository::class] = function ($container) {
            return new SqlThingRepository($container['connection']);
        };
        // @codeCoverageIgnoreEnd

        $container[PasswordHashing::class] = function () {
            return new Md5PasswordHashing(getenv('APP_SALT'));
        };

        // Domain Services
        $container[ThingService::class] = function ($container) {
            return new ThingService(
                $container[ThingRepository::class]
            );
        };

        $container[UserService::class] = function ($container) {
            return new UserService(
                $container[UserRepository::class],
                $container[PasswordHashing::class]
            );
        };

        // Controllers
        $container[ThingController::class] = function ($container) {
            return new ThingController(
                $container[ThingService::class],
                $container[ResponseTransformer::class]
            );
        };

        $container[UserController::class] = function ($container) {
            return new UserController(
                $container[UserService::class],
                $container[ResponseTransformer::class]
            );
        };

        // Middleware
        $container[UserFromBasicAuthMiddleware::class] = function ($container) {
            return new UserFromBasicAuthMiddleware(
                $container[UserRepository::class],
                $container[PasswordHashing::class]
            );
        };

        $container[Handler::class] = function ($container) {
            $bugsnag = Client::make(getenv('BUGSNAG_API_KEY'));
            $bugsnag->setReleaseStage('development');

            return new Handler(
                $container[ResponseTransformer::class],
                $bugsnag
            );
        };

        $container['errorHandler'] = function ($container) {
            return $container[Handler::class];
        };

        $container['notFoundHandler'] = function () {
            throw new HttpException(404);
        };

        $container['notAllowedHandler'] = function ($container) {
            return function (Request $request, Response $response, $methods) use ($container) {
                $code = 405;
                $response = $response->withStatus($code)
                    ->withHeader('Allow', implode(', ', $methods))
                    ->withHeader('Content-type', $container[ResponseTransformer::class]::CONTENT_TYPE);
                return $container[ResponseTransformer::class]->transform(
                    $response,
                    [],
                    [
                        [
                            'code' => $code,
                            'message' => 'Method must be one of: ' . implode(', ', $methods)
                        ]
                    ]
                );
            };
        };

        return $app;
    }
}
