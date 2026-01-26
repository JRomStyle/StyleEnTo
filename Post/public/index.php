<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

use App\Core\Router;
use App\Core\Request;

$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

$response = $router->dispatch(new Request());
$response->send();
