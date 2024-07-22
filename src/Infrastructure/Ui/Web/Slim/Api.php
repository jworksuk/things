<?php

namespace Things\Infrastructure\Ui\Web\Slim;

use Slim\App;
use Things\Application\DataTransformer\Response\JsonResponseTransformer;
use Things\Application\DataTransformer\Response\ResponseTransformer;
use Things\Application\Http\Controller\ThingController;
use Things\Application\Http\Controller\UserController;
use Things\Application\Http\Middleware\UserFromBasicAuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Api extends Application
{
    public static function bootstrap(): App
    {
        $app = parent::bootstrap();
        $app->setBasePath('/api');

        $container = $app->getContainer();

        $container->set(ResponseTransformer::class, function () {
            return new JsonResponseTransformer;
        });

        // Users endpoints
        $app->get('/', function (Request $request, Response $response) {
            $response->getBody()->write('Hello?');
            return $response;
        });
        $app->post('/users', [UserController::class, 'create']);
        $app->get('/users/{id}', [UserController::class, 'show'])->add(UserFromBasicAuthMiddleware::class);


        // Things endpoints
        $app->group('', function () use ($app) {
            $app->get('/things', ThingController::class.':list');
            $app->post('/things', ThingController::class.':create');
            $app->get('/things/{id}', ThingController::class.':read');
            $app->put('/things/{id}', ThingController::class.':update');
            $app->delete('/things/{id}', ThingController::class.':delete');
        })->add(UserFromBasicAuthMiddleware::class);

        return $app;
    }
}
