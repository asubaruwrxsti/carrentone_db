<?php
    session_start();
    require_once 'vendor/autoload.php';
    include_once 'Database.php';
    $db = new DB('localhost', 'root', '', 'carrentone');

    if (isset($_POST['login'])) {
        $username = mysqli_real_escape_string($db, $_POST['username']);
        $password = mysqli_real_escape_string($db, $_POST['password']);
        $password = md5($password);

        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($db, $sql);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['username'] = $username;
            setcookie('username', $username, time() + 3600, '/');
            header("Location: admin_dashboard.php");
        } else {
            echo "failed";
        }
    }

    if (isset($_POST['logout'])) {
        $db->close_connection($db);
        session_destroy();
        setcookie('username', '', time() - 3600, '/');
        header("Location: Login.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $loader = new \Twig\Loader\FilesystemLoader('views');
        $twig = new \Twig\Environment($loader);

        if (isset($_SESSION['username'])) {
            header("Location: index.php");
            die();
        }

        $header = $twig->load('/assets/header.twig');
        $template = $twig->load('/login/login.twig');

        echo $header->render(array());
        echo $template->render(array(
            'window_title' => 'Login',
            'title' => 'Login',
            'content' => 'Login'
        ));
    }

?>