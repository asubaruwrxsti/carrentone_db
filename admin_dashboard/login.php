<?php
    session_start();
    require_once 'vendor/autoload.php';
    include_once 'Database.php';

    $db = new DB('localhost', 'root', '', 'carrentone');
    $purifier_config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($purifier_config);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $loader = new \Twig\Loader\FilesystemLoader('views');
        $twig = new \Twig\Environment($loader);

        if (isset($_SESSION['is_loggedin'])) {
            header("Location: /admin_dasboard/index.php");
            die();
        }

        $header = $twig->load('/assets/header.twig');
        $template = $twig->load('/login/login.twig');

        echo $header->render(array(
            'window_title' => 'Login',
            'user_logged_in' => false
        ));
        echo $template->render(array(
            'title' => 'Login',
            'content' => 'Login'
        ));
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $username = $purifier->purify($_POST['username']);
        $password = $purifier->purify($_POST['password']);
        $password = md5($password);

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = $db->execute_query($sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_role'] = $row['is_admin'] == 1 ? 'admin' : 'user';
            $_SESSION['username'] = $username;
            $_SESSION['is_loggedin'] = true;
            
            setcookie('username', $username, time() + 3600, '/');
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Login successful'
            ));
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Wrong Credentials'
            ));
        }
    }

?>