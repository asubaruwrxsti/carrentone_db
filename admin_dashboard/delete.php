<?php

require_once 'database.php';
error_reporting(E_ALL ^ E_NOTICE);
$db = new Database();

session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
}

$car_id = $_GET['id'];

$sql = "SELECT name FROM cars WHERE id = $car_id";
$result = $db->query($sql);
$result = $result->fetch_assoc();

$car_name = $result['name'];

$sql = "DELETE FROM cars WHERE id = $car_id";
$res = $db->query($sql);

if($res) {
    $dir = "../assets/car_pages/".$car_name;
    $files = glob($dir . '/*'); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file))
            unlink($file); // delete file
    }
    rmdir($dir);

    $dir = "../images/car_images/".$car_name;
    $files = glob($dir . '/*'); // get all file names
    foreach($files as $file){ // iterate files
        if(is_file($file))
            unlink($file); // delete file
    }
    rmdir($dir);
    header('Location: index.php');
} else {
    echo "Error";
}


header("Location: index.php");