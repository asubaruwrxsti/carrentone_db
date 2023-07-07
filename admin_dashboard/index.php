<?php
    session_start();
    if (!isset($_SESSION['is_loggedin'])) {
        header("Location: /admin_dashboard/Login.php");
    }

    require_once 'vendor/autoload.php';
    require_once 'Database.php';

    $loader = new \Twig\Loader\FilesystemLoader('views');
    $twig = new \Twig\Environment($loader);

    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addGroup('/admin_dashboard/index.php', function ($r) {
            $r->addRoute('GET', '/', 'index');
            $r->addRoute('GET', '/logout', 'logout');
            $r->addRoute('GET', '/cars', 'cars');
            $r->addRoute('GET', '/reports', 'reports');
            $r->addRoute('GET', '/messages', 'messages');
            $r->addRoute('GET', '/orders', 'orders');
            $r->addRoute('GET', '/users', 'users');
        });
    });

    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }

    $uri = rawurldecode($uri);
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    
    $header = $twig->load('/assets/header.twig');
    $base = $twig->load('base.twig');

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            echo $twig->render('/assets/404.twig');
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            echo $twig->render('/assets/404.twig');
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            if ($handler == 'handler') {
                $handler = $vars['REQUEST_URI'];
            }

            switch ($handler) {
                case 'logout':
                    session_destroy();
                    header("Location: /admin_dashboard/Login.php");
                    break;

                case 'index':
                    echo $header->render(array(
                        'window_title' => 'Dashboard',
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => 'admin',
                        'user_name' => strtoupper($_SESSION['username'])
                    ));
                    echo $base->render(array(
                        'window_title' => 'Dashboard',
                        'content' => $twig->display('/dashboard/dashboard.twig')
                    ));
                    break;
            }
            break;
    }
?>