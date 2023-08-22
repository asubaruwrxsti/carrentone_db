<?php
    require_once 'vendor/autoload.php';
    require_once 'admin_dashboard/database.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    $db = new DB($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);

    $loader = new \Twig\Loader\FilesystemLoader('views');
    $twig = new \Twig\Environment($loader);

    $purifier_config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($purifier_config);

    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/', 'home');
        $r->addRoute('GET', '/index.php/view/{id:\d+}', 'cars');
        $r->addRoute('POST', '/index.php/message/', 'messages');
    });

    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }

    $uri = rawurldecode($uri);
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            echo $twig->render('404.twig');
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            echo $twig->render('404.twig');
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            if ($handler == 'handler') {
                $handler = $vars['REQUEST_URI'];
            }

            switch ($handler) {
                case 'home':
                    $cars = $db->execute_query('SELECT * FROM cars');
                    $cars = $cars->fetch_all(MYSQLI_ASSOC);

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

                    $home = $twig->load(sprintf('/%s/%s.twig', $handler, $handler));
                    echo $home->render([
                        'title' => strtoupper($handler),
                        'cars' => $cars
                    ]);
                    break;

                case 'cars':
                    $id = $vars['id'];
                    $sql = "SELECT * FROM cars WHERE id = $id";
                    $car = $db->execute_query($sql);
                    $car = $car->fetch_assoc();

                    $sql = "SELECT * FROM cars";
                    $allCars = $db->execute_query($sql);
                    $allCars = $allCars->fetch_all(MYSQLI_ASSOC);

                    $imagePath = "/admin_dashboard/views/assets/images/";
                    $rootPath = $_SERVER['DOCUMENT_ROOT'];
                    $car_images = glob($rootPath . $imagePath . $id . "/{*.jpg,*.jpeg,*.png}", GLOB_BRACE);
                    $car_images = array_map(function($image) use ($rootPath) {
                        return str_replace($rootPath, '', $image);
                    }, $car_images);
                    if (count($car_images) == 0) {
                        $car_images = array('/admin_dashboard/views/assets/img/noImg.jpg');
                    }
                    $car['image'] = $car_images;
                    $allCars = array_map(function($car) use ($rootPath, $imagePath) {
                        $car_id = $car['id'];
                        $car_images = glob($rootPath . $imagePath . $car_id . "/{*.jpg,*.jpeg,*.png}", GLOB_BRACE);
                        $car_images = array_map(function($image) use ($rootPath) {
                            return str_replace($rootPath, '', $image);
                        }, $car_images);
                        if (count($car_images) == 0) {
                            $car_images = array('/admin_dashboard/views/assets/img/noImg.jpg');
                        }
                        $car['image'] = $car_images;
                        return $car;
                    }, $allCars);
                    
                    $view = $twig->load(sprintf('/%s/%s.twig', $handler, $handler));
                    echo $view->render([
                        'title' => strtoupper($handler),
                        'car' => $car,
                        'allCars' => $allCars
                    ]);
                    break;
                
                case 'messages':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $name = $purifier->purify($data['firstname']);
                    $lastname = $purifier->purify($data['lastname']);
                    $email = $purifier->purify($data['email']);
                    $phone_number = $purifier->purify($data['phone']);
                    $message = $purifier->purify($data['message']);

                    // format the data like this {"senders":[{"name":"Alice","email":"alice@example.com","lastname":"Johnson"}],"data":[{"id":1,"sender":"userA","content":"Hello there!","timestamp":"2023-07-21T10:30:00"}]}
                    $message = json_encode([
                        'senders' => [
                            [
                                'name' => $name,
                                'lastname' => $lastname,
                                'email' => $email,
                                'phone' => $phone_number,
                            ]
                        ],
                        'data' => [
                            [
                                'sender' => $name,
                                'content' => $message,
                                'timestamp' => date('Y-m-d H:i:s')
                            ]
                        ]
                    ]);

                    $sql = "INSERT INTO messages (data) VALUES ('$message')";
                    $db->execute_query($sql);
                    echo json_encode(['status' => true]);
                    break;
            }
        break;
    }
?>