<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: Login.php");
    }

    require_once 'vendor/autoload.php';
    require_once 'Database.php';

    $loader = new \Twig\Loader\FilesystemLoader('views');
    $twig = new \Twig\Environment($loader);

    $header = $twig->load('/assets/header.twig');
    echo $header->render(array(
        'window_title' => 'Admin Dashboard',
        'user_logged_in' => true,
        'user_name' => strtoupper($_SESSION['username'],)
    ));
?>