<?php

namespace Things\Infrastructure\Ui\Web\Slim;

use Slim\App;
use Exception;
use DI\Container;
use Dotenv\Dotenv;
use DI\ContainerBuilder;
use DI\Bridge\Slim\Bridge;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * Class Application
 * @package Things\Infrastructure\Ui\Web\Slim
 */
class Application extends App
{
    /**
     * Bootstrap app.
     * @throws Exception
     */
    public static function bootstrap(): App
    {
        // Load .env file
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../../../../');
        $dotenv->load();
        $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS']);

        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();

        $app =  Bridge::create(static::buildContainer());

        $routeCollector = $app->getRouteCollector();
        $routeCollector->setDefaultInvocationStrategy(new RequestResponseArgs());

        return $app;
    }

    /**
     * @throws Exception
     */
    protected static function buildContainer(): Container
    {
        $builder = new ContainerBuilder();
        return $builder->useAutowiring(true)
            ->useAttributes(false)
            ->addDefinitions(__DIR__ . '/di-definitions.php')
            ->build();
    }
}
