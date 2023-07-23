<?php
    session_start();
    if (!isset($_SESSION['is_loggedin'])) {
        header("Location: /admin_dashboard/Login.php");
    }

    require_once 'vendor/autoload.php';
    require_once 'Database.php';
    $db = new DB('localhost', 'root', '', 'carrentone');

    $loader = new \Twig\Loader\FilesystemLoader('views');
    $twig = new \Twig\Environment($loader);

    $purifier_config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($purifier_config);

    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addGroup('/admin_dashboard/index.php', function ($r) {

            // Pages
            $r->addRoute('GET', '/', 'Dashboard');
            $r->addRoute('GET', '/cars', 'Cars');
            $r->addRoute('GET', '/reports', 'Reports');
            $r->addRoute('GET', '/messages', 'Messages');
            $r->addRoute('GET', '/orders', 'Orders');
            $r->addRoute('GET', '/users', 'Users');

            // API
            $r->addRoute('GET', '/api/{property}/', 'api');
            $r->addRoute('GET', '/api/{property}/{id:\d+}', 'api');

            // UPSERT
            $r->addRoute(['POST', 'DELETE'], '/api/{property}/edit/{id:\d+}', 'editapi');
            $r->addRoute('POST', '/api/{property}/edit/', 'editapi');

            // VIEW
            $r->addRoute('GET', '/view/{property}/{id:\d+}', 'viewapi');

            // Logout
            $r->addRoute('GET', '/logout', 'logout');
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
                    $db->destroy_session_id($_SESSION['user_id']);
                    $db->close_connection();
                    header("Location: /admin_dashboard/Login.php");
                    break;
                
                case 'api':
                    require_once 'api.php';
                    $api = new API($db);
                    $property = $purifier->purify($vars['property']);
                    $id = isset($vars['id']) ? $purifier->purify($vars['id']) : null;

                    $res = $api->fetch_data([$property, $id]);
                    echo json_encode($res);
                    break;
                
                case 'editapi':
                    require_once 'api.php';
                    $api = new API($db);
                    $property = $purifier->purify($vars['property']);
                    $id = isset($vars['id']) ? $purifier->purify($vars['id']) : null;

                    $res = $api->fetch_data([$property, $id]);
                    echo json_encode($res);
                    break;

                case 'viewapi':
                    require_once 'api.php';
                    $api = new API($db);
                    $property = $purifier->purify($vars['property']);
                    $id = isset($vars['id']) ? $purifier->purify($vars['id']) : null;

                    $data = $api->fetch_data([$property, $id]);

                    if ($property == 'customers') {
                        $sql = "SELECT * FROM revenue INNER JOIN cars ON revenue.car_id = cars.id WHERE revenue.customer_id = $id;";
                        $result = $db->execute_query($sql);
                        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    }
                    var_dump($result);

                    echo $header->render(array(
                        'window_title' => sprintf('%s %s - %s', $data[0]['firstname'], $data[0]['lastname'], strtoupper($property)),
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username'])
                    ));
        
                    echo $base->render(array(
                            'window_title' => sprintf('%s %s', $data[0]['firstname'], $data[0]['lastname']),
                            'content' => sprintf('/%s/%s.twig', $handler, $handler),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                                'property' => $property,
                                'data' => $data[0],
                                'related_data' => $result
                            ]
                        )
                    );
                    break;

                case 'Dashboard':
                    $recentOrders = "SELECT customers.firstname, customers.lastname, customers.phone_number, cars.name, cars.id, revenue.rental_date, revenue.rental_duration
                        FROM revenue 
                        JOIN customers 
                        ON customers.id = revenue.customer_id 
                        JOIN cars ON cars.id = revenue.car_id
                        ORDER BY revenue.rental_date DESC LIMIT 5;";
                    $recentOrders = $db->execute_query($recentOrders);
                    $recentOrders = $recentOrders->fetch_all(MYSQLI_ASSOC);

                    echo $header->render(array(
                        'window_title' => $handler,
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username'])
                    ));
                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', $handler, $handler),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                                'recent_orders' => $recentOrders
                            ]
                        )
                    );
                    break;
                
                case 'Cars':
                    $cars = "SELECT * FROM cars;";
                    $cars = $db->execute_query($cars);
                    $cars = $cars->fetch_all(MYSQLI_ASSOC);

                    $imagePath = "/admin_dashboard/views/cars/images/";
                    $rootPath = $_SERVER['DOCUMENT_ROOT'];
                    foreach ($cars as $key => $car) {
                        $car_id = $car['id'];
                        $car_images = glob($rootPath . $imagePath . $car_id . "/{*.jpg,*.jpeg,*.png}", GLOB_BRACE);
                        $car_images = array_map(function($image) use ($rootPath) {
                            return str_replace($rootPath, '', $image);
                        }, $car_images);
                        if (count($car_images) == 0) {
                            $car_images = array('/admin_dashboard/views/assets/img/noImg.jpg');
                        }
                        $cars[$key]['image'] = $car_images[0];
                    }

                    echo $header->render(array(
                        'window_title' => $handler,
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username'])
                    ));
                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', $handler, $handler),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                                'cars' => $cars
                            ]
                        )
                    );
                    break;

                case 'Messages':
                    echo $header->render(array(
                        'window_title' => $handler,
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username'])
                    ));
                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', $handler, $handler),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                            ]
                        )
                    );
            }
            break;
    }
?>