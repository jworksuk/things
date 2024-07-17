<?php

use Bugsnag\Client;
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
use Things\Infrastructure\Domain\Service\Md5PasswordHashing;
use Things\Infrastructure\Persistence\Sql\SqlThingRepository;
use Things\Infrastructure\Persistence\Sql\SqlUserRepository;
use Things\Infrastructure\Ui\Web\Slim\Handler;
use Psr\Container\ContainerInterface as Container;

return [
    'config' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'db' => [
            'host' => $_SERVER['DB_HOST'],
            'user' => $_SERVER['DB_USER'],
            'pass' => $_SERVER['DB_PASS'],
            'name' => $_SERVER['DB_NAME'],
        ]
    ],
    'connection' => function (Container $c) {
        $config = $c->get('config');
        $db = $config['db'];

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
    // @codeCoverageIgnoreEnd

    PasswordHashing::class => function () {
        return new Md5PasswordHashing($_SERVER['APP_SALT']);
    },

    // Domain Services
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

    // Controllers
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

    Handler::class => function (Container $container) {
        $bugsnag = Client::make($_SERVER['BUGSNAG_API_KEY']);
        $bugsnag->setReleaseStage('development');

        return new Handler(
            $container[ResponseTransformer::class],
            $bugsnag
        );
    },

    'errorHandler' => function (Container $container) {
        return $container[Handler::class];
    },

    'notFoundHandler' => function () {
        throw new HttpException(404);
    },

//    'notAllowedHandler' => function(Container $container) {
//        return function (Request $request, Response $response, $methods) use ($container) {
//            $code = 405;
//            $response = $response->withStatus($code)
//                ->withHeader('Allow', implode(', ', $methods))
//                ->withHeader('Content-type', $container[ResponseTransformer::class]::CONTENT_TYPE);
//            return $container[ResponseTransformer::class]->transform(
//                $response,
//                [],
//                [
//                    [
//                        'code' => $code,
//                        'message' => 'Method must be one of: ' . implode(', ', $methods)
//                    ]
//                ]
//            );
//        };
//    },
];
