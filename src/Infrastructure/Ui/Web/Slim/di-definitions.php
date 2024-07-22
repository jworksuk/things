<?php

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Things\Application\DataTransformer\Response\ResponseTransformer;
use Things\Application\Http\Controller\ThingController;
use Things\Application\Http\Controller\UserController;
use Things\Application\Http\Middleware\UserFromBasicAuthMiddleware;
use Things\Application\Service\ThingService;
use Things\Application\Service\UserService;
use Things\Domain\Model\Thing\ThingRepository;
use Things\Domain\Model\User\UserRepository;
use Things\Domain\Service\PasswordHashing;
use Things\Infrastructure\Domain\Service\Md5PasswordHashing;
use Things\Infrastructure\Persistence\Sql\SqlThingRepository;
use Things\Infrastructure\Persistence\Sql\SqlUserRepository;
use Psr\Container\ContainerInterface as Container;

return [
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    'db' => [
        'host' => $_SERVER['DB_HOST'],
        'user' => $_SERVER['DB_USER'],
        'pass' => $_SERVER['DB_PASS'],
        'name' => $_SERVER['DB_NAME'],
    ],
    'connection' => function (Container $c) {
        $db = $c->get('db');

        return new PDO(
            "mysql:host=". $db['host'] .";dbname=".$db['name'],
            $db['user'],
            $db['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    },

    UserRepository::class => function (Container $container) {
        return new SqlUserRepository($container->get('connection'));
    },

    ThingRepository::class => function (Container  $container) {
        return new SqlThingRepository($container->get('connection'));
    },

    PasswordHashing::class => function () {
        return new Md5PasswordHashing($_SERVER['APP_SALT']);
    },

    ThingService::class => function (Container $container) {
        return new ThingService(
            $container->get(ThingRepository::class)
        );
    },

    UserService::class => function (Container $container) {
        return new UserService(
            $container->get(UserRepository::class),
            $container->get(PasswordHashing::class)
        );
    },

    ThingController::class => function (Container $container) {
        return new ThingController(
            $container->get(ThingService::class),
            $container->get(ResponseTransformer::class)
        );
    },

    UserController::class => function (Container $container) {
        return new UserController(
            $container->get(UserService::class),
            $container->get(ResponseTransformer::class)
        );
    },

    // Middleware
    UserFromBasicAuthMiddleware::class => function (Container $container) {
        return new UserFromBasicAuthMiddleware(
            $container->get(UserRepository::class),
            $container->get(PasswordHashing::class)
        );
    },

    LoggerInterface::class => function (Container $container) {
        $logger = new Logger('app');
        $logger->pushHandler(new RotatingFileHandler(
            __DIR__ . '/../../../../../storage/logs/app.log',
            0
        ));
        return $logger;
    },
];
