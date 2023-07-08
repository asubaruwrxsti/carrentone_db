<?php
    session_start();
    if (!isset($_SESSION['is_loggedin'])) {
        header("Location: /admin_dashboard/Login.php");
    }

    require_once 'vendor/autoload.php';
    require_once 'Database.php';

    $loader = new \Twig\Loader\FilesystemLoader('views');
    $twig = new \Twig\Environment($loader);

    $purifier_config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($purifier_config);

    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addGroup('/admin_dashboard/index.php', function ($r) {
            $r->addRoute('GET', '/', 'Dashboard');
            $r->addRoute('GET', '/logout', 'logout');
            $r->addRoute('GET', '/cars', 'Cars');
            $r->addRoute('GET', '/reports', 'Reports');
            $r->addRoute('GET', '/messages', 'Messages');
            $r->addRoute('GET', '/orders', 'Orders');
            $r->addRoute('GET', '/users', 'Users');
            $r->addRoute(['GET', 'POST'], '/api/[/{property}]/{id:\d+}', 'api');
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
                
                case 'api':
                    $api = new API(new DB('localhost', 'root', '', 'carrentone'));
                    $property = $purifier->purify($vars['property']);
                    $id = $purifier->purify($vars['id']);

                    $api->fetch_data([$property, $id]);
                    break;

                case 'Dashboard':
                    echo $header->render(array(
                        'window_title' => $handler,
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username'])
                    ));
                    echo $base->render(array(
                        'window_title' => $handler,
                        'content' => sprintf('/%s/%s.twig', $handler, $handler),
                        'vars' =>   [
                                'type' => 'bar',
                                'data' => [
                                    'labels' => ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                                    'datasets' => [
                                        'label' => '# of Votes',
                                        'data' => [12, 19, 3, 5, 2, 3],
                                        'borderWidth' => 1
                                    ],
                                    'options' => [
                                        'scales' => [
                                            'y' => [
                                                'beginAtZero' => true
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        )
                    );
                    break;
            }
            break;
    }
?>