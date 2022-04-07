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
$db->query($sql);

header("Location: index.php");