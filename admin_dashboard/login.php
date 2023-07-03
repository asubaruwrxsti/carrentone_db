<?php
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
            echo "Invalid username or password";
        }
    }

    if (isset($_POST['logout'])) {
        $db->close_connection($db);
        session_destroy();
        setcookie('username', '', time() - 3600, '/');
        header("Location: Login.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $loader = new Twig_Loader_Filesystem('views');
        $twig = new Twig_Environment($loader, array());
    
        echo $twig->render('base.twig', [
            'title' => 'Login',
            'content' => 'hello world'
        ]);
    }

?>