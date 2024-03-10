<?php

namespace Router;

use FastRoute;
use DI\ContainerBuilder;
use App\Controllers\UserController;
use App\Services\Config;
use PDO;
use League\Plates\Engine;
use Delight\Auth\Auth;
use Aura\SqlQuery\QueryFactory;
use Tamtamchik\SimpleFlash\Flash;

Class Router 
{
    private static $dispatcher;

    public static function getRouter(): void
    {
        self::$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/users', [UserController::class, 'getPageUsers']);

            $r->addRoute('GET', '/login', [UserController::class, 'getPageLogin']);
            $r->addRoute('POST', '/login', [UserController::class, 'login']);

            $r->addRoute('GET', '/logout', [UserController::class, 'logOut']);

            $r->addRoute('GET', '/register', [UserController::class, 'getPageRegister']);
            $r->addRoute('POST', '/register', [UserController::class, 'register']);

            $r->addRoute('GET', '/create_user', [UserController::class, 'getPageCreateUser']);
            $r->addRoute('POST', '/create_user', [UserController::class, 'createUser']);

            // другой контроллер, так как данные для страницы берутся из id в uri
            $r->addRoute('GET', '/profile/{id:\d+}', [UserController::class, 'getPageProfile']);
            $r->addRoute('GET', '/delete-user/{id:\d+}', [UserController::class, 'deleteUser']);

            $r->addRoute('GET', '/edit/{id:\d+}', [UserController::class, 'getPageEdit']);
            $r->addRoute('POST', '/edit/{id:\d+}', [UserController::class, 'editUser']);

            $r->addRoute('GET', '/image/{id:\d+}', [UserController::class, 'getPageImage']);
            $r->addRoute('POST', '/image/{id:\d+}', [UserController::class, 'imageUser']);

            $r->addRoute('GET', '/status/{id:\d+}', [UserController::class, 'getPageStatus']);
            $r->addRoute('POST', '/status/{id:\d+}', [UserController::class, 'statusUser']);

            $r->addRoute('GET', '/security/{id:\d+}', [UserController::class, 'getPageSecurity']);
            $r->addRoute('POST', '/security/{id:\d+}', [UserController::class, 'securityUser']);
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(
            [
                PDO::class => function() {
                return new PDO("mysql:host=" . Config::get('mysql.host'). "; dbname=" . Config::get('mysql.database'), Config::get('mysql.username'), Config::get('mysql.password'));
                },

                Engine::class => function() {
                return new Engine('../Views');
                },
                Auth::class => function($container) {
                    return new Auth($container->get('PDO'));
                },
                QueryFactory::class => function() {
                    return new QueryFactory('mysql');
                },
                Flash::class => function() {
                    return new Flash;
                }
            ]
        );
        $container = $containerBuilder->build();

        $routeInfo = self::$dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                echo 404;
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                echo 405;
                break;
            case FastRoute\Dispatcher::FOUND:
                $vars = $routeInfo[2];
                //preg_match("#^/(\w+)#", $uri, $matches);
                //$vars['page']=$matches[1];
                $handler = $routeInfo[1];
                $container->call($handler, [$vars]);
                break;
        }
    }
}
