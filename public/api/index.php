<?php

require_once __DIR__.'/../../vendor/autoload.php';

$app = Things\Infrastructure\Ui\Web\Slim\Api::bootstrap();

$app->run();
