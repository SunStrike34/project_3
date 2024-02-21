<?php

namespace Router;

use FastRoute;
use DI\ContainerBuilder;
use App\Controllers\RenderController;
use App\Controllers\UserController;
use App\Services\Config;
use PDO;
use League\Plates\Engine;
use Delight\Auth\Auth;


Class Router 
{
    private static $dispatcher;

    public static function getRouter(): void
    {
        self::$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/users', [RenderController::class, 'getPage', ['users']]);

            $r->addRoute('GET', '/login', [RenderController::class, 'getPage', ['page_login']]);
            $r->addRoute('POST', '/login', [UserController::class, 'login']);

            $r->addRoute('GET', '/logout', [UserController::class, 'logOut']);

            $r->addRoute('GET', '/register', [RenderController::class, 'getPage', ['page_register']]);
            $r->addRoute('POST', '/register', [UserController::class, 'register']);

            $r->addRoute('GET', '/create_user', [RenderController::class, 'getPage', ['create_user']]);
            $r->addRoute('POST', '/create_user', [UserController::class, 'createUser']);

            $r->addRoute('GET', '/profile/{id:\d+}', [RenderController::class, 'getPage', ['page_profile']]);

            $r->addRoute('GET', '/edit/{id:\d+}[/{page}]', [RenderController::class, 'getPage']);
            $r->addRoute('POST', '/edit/{id:\d+}', [UserController::class, 'editUser']);

            $r->addRoute('GET', '/image/{id:\d+}', [RenderController::class, 'getPage', ['image']]);
            $r->addRoute('POST', '/image/{id:\d+}', [UserController::class, 'imageUser']);

            $r->addRoute('GET', '/status/{id:\d+}', [RenderController::class, 'getPage', ['status']]);
            $r->addRoute('POST', '/status/{id:\d+}', [UserController::class, 'statusUser']);

            $r->addRoute('GET', '/security/{id:\d+}', [RenderController::class, 'getPage', ['security']]);
            $r->addRoute('POST', '/security/{id:\d+}', [UserController::class, 'securityUser']);
            /*
            // {id} must be a number (\d+)
            $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
            // The /{title} suffix is optional
            $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
            */
        });
        
        // Fetch method and URI from somewhere
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        // Strip query string (?foo=bar) and decode URI
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
                //if (isset($routeInfo[1][2])) {
                //    $vars = $routeInfo[1][2];
                //    unset($routeInfo[1][2]);
                //} else {
                    $vars = $routeInfo[2];
                //}
                $handler = $routeInfo[1];
                d($routeInfo,$handler, $vars, $uri);die();
                $container->call($handler, [$vars]);
                break;
        }
    }
}