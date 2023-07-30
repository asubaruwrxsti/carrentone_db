<?php
    /**
     * Main Routing file
     * @package admin_dashboard
     */

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

    $dompdf = new Dompdf\Dompdf();

    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addGroup('/admin_dashboard/index.php', function ($r) {

            // Pages
            $r->addRoute('GET', '/', 'Dashboard');
            $r->addRoute('GET', '/cars', 'Cars');
            $r->addRoute('GET', '/reports', 'Reports');
            $r->addRoute('GET', '/messages', 'Messages');
            $r->addRoute('GET', '/orders', 'Orders');
            $r->addRoute('GET', '/customers', 'Customers');

            // API
            $r->addRoute('GET', '/api/{property}/', 'api');
            $r->addRoute('GET', '/api/{property}/{id:\d+}', 'api');

            // UPSERT
            $r->addRoute(['POST', 'DELETE'], '/api/{property}/edit/{id:\d+}', 'upsertapi');
            $r->addRoute(['POST', 'DELETE'], '/api/{property}/edit/', 'upsertapi');

            // VIEW
            $r->addRoute('GET', '/view/{property}/{id:\d+}', 'viewapi');

            // INVOICE
            $r->addRoute('GET', '/invoice/{id:\d+}', 'invoice');

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
                
                case 'upsertapi':
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
                        $sql = "SELECT revenue.id, cars.id AS car_id, cars.name AS car_name, revenue.rental_date, revenue.rental_duration, revenue.price 
                            FROM revenue 
                            INNER JOIN cars ON revenue.car_id = cars.id 
                            WHERE revenue.customer_id = $id;";
                        $result = $db->execute_query($sql);
                        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        $window_title = sprintf('%s %s', $data[0]['firstname'], $data[0]['lastname']);
                    } else if ($property == 'cars') {
                        $sql = "SELECT revenue.id, customers.id AS customer_id, customers.firstname, customers.lastname, revenue.rental_date, revenue.rental_duration, revenue.price 
                            FROM revenue 
                            INNER JOIN customers ON revenue.customer_id = customers.id 
                            WHERE revenue.car_id = $id;";
                        $result = $db->execute_query($sql);
                        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        $window_title = sprintf('%s', strtoupper($data[0]['name']));
                    }

                    echo $header->render(array(
                        'window_title' => sprintf('%s', strtoupper($property)),
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username'])
                    ));
        
                    echo $base->render(array(
                            'window_title' => $window_title,
                            'content' => sprintf('/%s/%s.twig', $handler, $handler),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                                'property' => $property,
                                'data' => $data[0],
                                'invoices' => $result,
                                'view_type' => $property
                            ]
                        )
                    );
                    break;
                
                case 'invoice':
                    require_once 'api.php';
                    $api = new API($db);

                    $id = isset($vars['id']) ? $purifier->purify($vars['id']) : null;

                    $sql = "SELECT revenue.id, customers.id AS customer_id, customers.firstname, customers.lastname, revenue.rental_date, revenue.rental_duration, revenue.price, cars.name AS car_name
                        FROM revenue 
                        INNER JOIN customers ON revenue.customer_id = customers.id 
                        INNER JOIN cars ON revenue.car_id = cars.id 
                        WHERE revenue.id = $id;";
                    $result = $db->execute_query($sql);
                    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    
                    $html = $twig->render('base.twig', array(
                        'content' => sprintf('/%s/%s.twig', $handler, $handler),
                        'vars' => [
                            'currency' => $_SESSION['currency'],
                            'data' => $result[0]
                        ]
                    ));

                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    $dompdf->stream();
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
                
                case 'Orders':
                    break;
                
                case 'Customers':
                    $customers = "SELECT * FROM customers;";
                    $customers = $db->execute_query($customers);
                    $customers = $customers->fetch_all(MYSQLI_ASSOC);

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
                                'customers' => $customers,
                            ]
                        )
                    );
                    break;
            }
            break;
    }
?>