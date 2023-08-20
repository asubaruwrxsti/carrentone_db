<?php
    /**
     * Main Routing file
     * @package admin_dashboard
     */

    session_start();
    if (!isset($_SESSION['is_loggedin'])) {
        header("Location: /admin_dashboard/login.php");
    }

    require_once 'vendor/autoload.php';
    require_once 'database.php';
    require_once 'api.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $db = new DB($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
    $api = new API($db);
    $loader = new \Twig\Loader\FilesystemLoader('views');
    $twig = new \Twig\Environment($loader);

    $purifier_config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($purifier_config);

    $dompdf = new Dompdf\Dompdf();

    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addGroup('/admin_dashboard/index.php', function ($r) {

            // Pages
            $r->addRoute('GET', '/', 'Dashboard');
            $r->addRoute('GET', '/reports', 'Reports');
            $r->addRoute('GET', '/messages', 'Messages');
            $r->addRoute('GET', '/customers', 'Customers');
            $r->addRoute('GET', '/profile', 'Profile');
            $r->addRoute('GET', '/settings', 'Settings');

            // Orders
            $r->addRoute('GET', '/orders', 'Orders');
            $r->addRoute('GET', '/orders/add/', 'Orders');
            $r->addRoute('GET', '/orders/edit/{id:\d+}', 'Orders');
            $r->addRoute('GET', '/orders/delete/{id:\d+}', 'Orders'); // BACKLOG: this is workaround for delete

            // Cars
            $r->addRoute('GET', '/cars', 'Cars');
            $r->addRoute('GET', '/cars/edit/{id:\d+}', 'Cars');
            $r->addRoute(['PUT', 'DELETE'], '/cars/images/{id:\d+}', 'Cars');

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

	$messages = "SELECT * FROM messages WHERE archieved != 1;";
	$messages = $db->execute_query($messages);
	$messages = mysqli_num_rows($messages);

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
                    header("Location: /admin_dashboard/login.php");
                    break;
                
                case 'api':
                    $property = $purifier->purify($vars['property']);
                    $id = isset($vars['id']) ? $purifier->purify($vars['id']) : null;

                    $res = $api->fetch_data([$property, $id]);
                    echo json_encode($res);
                    break;
                
                case 'upsertapi':
                    $property = $purifier->purify($vars['property']);
                    $id = isset($vars['id']) ? $purifier->purify($vars['id']) : null;

                    $res = $api->fetch_data([$property, $id]);
                    echo json_encode($res);
                    break;

                case 'viewapi':
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
                        'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
                    ));
        
                    echo $base->render(array(
                            'window_title' => $window_title,
                            'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
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
                    $id = isset($vars['id']) ? $purifier->purify($vars['id']) : null;
                    $sql = "SELECT customers.id AS customer_id, customers.firstname, customers.lastname, email, phone_number, birthdate, address, id_number,
                         revenue.id, revenue.rental_date, revenue.rental_duration, revenue.price, 
                         cars.name AS car_name, cars.make, cars.model, cars.year, cars.color, cars.license_plate
                        FROM revenue 
                        INNER JOIN customers ON revenue.customer_id = customers.id 
                        INNER JOIN cars ON revenue.car_id = cars.id 
                        WHERE revenue.id = $id;";
                    $result = $db->execute_query($sql);
                    $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    
                    $html = $base->render(array(
                        'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
                        'vars' => [
                            'currency' => $_SESSION['currency'],
                            'data' => $result
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
                        'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
                    ));
                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                                'recent_orders' => $recentOrders
                            ]
                        )
                    );
                    break;
                
                case 'Cars':
                    if (strpos($_SERVER['REQUEST_URI'], '/cars/images/') && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
                        $id = $purifier->purify($vars['id']);
                        $data = json_decode(file_get_contents('php://input'), true);
                        $response = $api->deleteImage($id, $data['image_index']);
                        echo json_encode($response);
                        break;
                    }

                    if (strpos($_SERVER['REQUEST_URI'], '/cars/images/') && $_SERVER['REQUEST_METHOD'] == 'PUT') {
                        $id = $purifier->purify($vars['id']);
                        $data = json_decode(file_get_contents('php://input'), true);
                        $response = $api->insertImage($id, $data['image']);
                        echo json_encode($response);
                        break;
                    }

                    $cars = $api->fetch_data(['cars']);
                    $imagePath = "/admin_dashboard/views/assets/images/";
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
                        $cars[$key]['image'] = $car_images;
                    }

                    if (strpos($_SERVER['REQUEST_URI'], '/cars/edit/')) {
                        $id = $purifier->purify($vars['id']);
                        echo $header->render(array(
                            'window_title' => $handler,
                            'user_logged_in' => $_SESSION['is_loggedin'],
                            'user_role' => $_SESSION['user_role'],
                            'user_name' => strtoupper($_SESSION['username']),
                            'message_count' => $messages
                        ));

                        echo $base->render(array(
                                'window_title' => 'Edit Car',
                                'content' => sprintf('/%s/%s.twig', strtolower($handler), 'editCars'),
                                'vars' => [
                                    'currency' => $_SESSION['currency'],
                                    'cars' => $cars[$id - 1]
                                ]
                            )
                        );
                        break;
                    }

                    echo $header->render(array(
                        'window_title' => $handler,
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
                    ));
                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
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
                        'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
                    ));
                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                            ]
                        )
                    );
                    break;
                
                /* Represents Revenue */
                case 'Orders':
                    $orders = "SELECT revenue.id, customers.firstname, customers.lastname, customers.phone_number, cars.name, cars.id AS carId, revenue.rental_date, revenue.rental_duration
                        FROM revenue 
                        JOIN customers 
                        ON customers.id = revenue.customer_id 
                        JOIN cars ON cars.id = revenue.car_id
                        ORDER BY revenue.rental_date DESC, revenue.rental_duration DESC;";
                    $orders = $db->execute_query($orders);
                    $orders = $orders->fetch_all(MYSQLI_ASSOC);

                    if (strpos($_SERVER['REQUEST_URI'], '/add/') !== false) {
                        $customers = "SELECT * FROM customers;";
                        $customers = $db->execute_query($customers);
                        $customers = $customers->fetch_all(MYSQLI_ASSOC);

                        $cars = "SELECT * FROM cars;";
                        $cars = $db->execute_query($cars);
                        $cars = $cars->fetch_all(MYSQLI_ASSOC);

                        echo $header->render(array(
                            'window_title' => $handler,
                            'user_logged_in' => $_SESSION['is_loggedin'],
                            'user_role' => $_SESSION['user_role'],
                            'user_name' => strtoupper($_SESSION['username']),
							'message_count' => $messages
                        ));
                        echo $base->render(array(
                                'window_title' => 'Add Order',
                                'content' => sprintf('/%s/%s.twig', strtolower($handler), 'addOrders'),
                                'vars' => [
                                    'currency' => $_SESSION['currency'],
                                    'customers' => $customers,
                                    'cars' => $cars
                                ]
                            )
                        );
                        break;
                    }

                    if (strpos($_SERVER['REQUEST_URI'], '/edit/')) {
                        $id = $purifier->purify($vars['id']);
                        $sql = "SELECT revenue.id, customers.id as cId, customers.firstname, customers.lastname, customers.phone_number, cars.name, cars.id AS carId, cars.price as carPrice, revenue.rental_date, revenue.rental_duration, revenue.price, revenue.notes
                            FROM revenue 
                            JOIN customers 
                            ON customers.id = revenue.customer_id 
                            JOIN cars ON cars.id = revenue.car_id
                            WHERE revenue.id = $id;";
                        $orders = $db->execute_query($sql);
                        $orders = $orders->fetch_all(MYSQLI_ASSOC);

                        $customers = "SELECT * FROM customers;";
                        $customers = $db->execute_query($customers);
                        $customers = $customers->fetch_all(MYSQLI_ASSOC);

                        $cars = "SELECT * FROM cars;";
                        $cars = $db->execute_query($cars);
                        $cars = $cars->fetch_all(MYSQLI_ASSOC);

                        echo $header->render(array(
                            'window_title' => $handler,
                            'user_logged_in' => $_SESSION['is_loggedin'],
                            'user_role' => $_SESSION['user_role'],
                            'user_name' => strtoupper($_SESSION['username']),
                            'message_count' => $messages
                        ));

                        echo $base->render(array(
                                'window_title' => 'Edit Order',
                                'content' => sprintf('/%s/%s.twig', strtolower($handler), 'editOrders'),
                                'vars' => [
                                    'currency' => $_SESSION['currency'],
                                    'customers' => $customers,
                                    'cars' => $cars,
                                    'orders' => $orders
                                ]
                            )
                        );
                        break;
                    }

                    if (strpos($_SERVER['REQUEST_URI'], '/delete/')) {
                        $id = $purifier->purify($vars['id']);
                        $sql = "DELETE FROM revenue WHERE id = $id;";
                        $db->execute_query($sql);
                        header("Location: /admin_dashboard/index.php/orders");
                        break;
                    }

                    echo $header->render(array(
                        'window_title' => $handler,
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
                    ));

                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                                'orders' => $orders
                            ]
                        )
                    );
                    break;
                
                case 'Customers':
                    $customers = $api->fetch_data(['customers']);
                    echo $header->render(array(
                        'window_title' => $handler,
                        'user_logged_in' => $_SESSION['is_loggedin'],
                        'user_role' => $_SESSION['user_role'],
                        'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
                    ));
                    echo $base->render(array(
                            'window_title' => $handler,
                            'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
                            'vars' => [
                                'currency' => $_SESSION['currency'],
                                'customers' => $customers,
                            ]
                        )
                    );
                    break;
				
				case 'Profile':
					$profile = "SELECT * FROM users WHERE username = '" . $_SESSION['username'] . "';";
					$profile = $db->execute_query($profile);
					$profile = $profile->fetch_all(MYSQLI_ASSOC);

					echo $header->render(array(
						'window_title' => $handler,
						'user_logged_in' => $_SESSION['is_loggedin'],
						'user_role' => $_SESSION['user_role'],
						'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
					));

					echo $base->render(array(
						'window_title' => $handler,
						'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
						'vars' => [
							'currency' => $_SESSION['currency'],
							'profile' => $profile
						]
					));
					break;
				
				case 'Settings':
					// BACKLOG: implement settings
					$settings = "SELECT * FROM settings;";
					$settings = $db->execute_query($settings);
					$settings = $settings->fetch_all(MYSQLI_ASSOC);

					echo $header->render(array(
						'window_title' => $handler,
						'user_logged_in' => $_SESSION['is_loggedin'],
						'user_role' => $_SESSION['user_role'],
						'user_name' => strtoupper($_SESSION['username']),
						'message_count' => $messages
					));

					echo $base->render(array(
						'window_title' => $handler,
						'content' => sprintf('/%s/%s.twig', strtolower($handler), strtolower($handler)),
						'vars' => [
							'currency' => $_SESSION['currency'],
							'settings' => $settings
						]
					));
					break;
            }
            break;
    }
?>