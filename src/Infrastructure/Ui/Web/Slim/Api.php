<?php

namespace Things\Infrastructure\Ui\Web\Slim;

use Slim\Http\Request;
use Things\Application\DataTransformer\Response\JsonResponseTransformer;
use Things\Application\DataTransformer\Response\ResponseTransformer;
use Things\Application\Http\Controller\ThingController;
use Things\Application\Http\Controller\UserController;
use Things\Application\Http\Middleware\UserFromBasicAuthMiddleware;

/**
 * Class Api
 * @package Things\Infrastructure\Ui\Web\Slim
 */
class Api extends Application
{
    /**
     * Bootstrap app.
     * @return Api|Application
     */
    public static function bootstrap()
    {
        $app = parent::bootstrap();

        $container = $app->getContainer();

        $container[ResponseTransformer::class] =  function () {
            return new JsonResponseTransformer;
        };

        // Users endpoints
        $app->post('/users', UserController::class.':create');
        $app->get('/users/{id}', UserController::class.':read')
            ->add(UserFromBasicAuthMiddleware::class);

        // Things endpoints
        $app->group('/things', function () use ($app) {
            $app->get('', ThingController::class.':list');
            $app->post('', ThingController::class.':create');
            $app->get('/{id}', ThingController::class.':read');
            $app->put('/{id}', ThingController::class.':update');
            $app->delete('/{id}', ThingController::class.':delete');
        })->add(UserFromBasicAuthMiddleware::class);

        return $app;
    }
}
