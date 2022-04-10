<!DOCTYPE html>

<style>
    input[type=text],
    select {
        width: 50%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    input[type=submit] {
        width: 50%;
        background-color: #4CAF50;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    input[type=submit]:hover {
        background-color: #45a049;
    }

    div {
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 20px;
    }
</style>

<?php
require_once 'database.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
}

?>

<form action="add.php" method="post" style="text-align: center;">
    <input type="text" name="car_name" placeholder="Emri">
    <input type="text" name="car_price" placeholder="Cmimi">
    <input type="text" name="car_desc" placeholder="Pershkrim">
    <select id='car_availability' name='car_availability'>
        <option value='0'> Jo Disponible </option>
        <option value='1'> Disponible </option>
    </select>
    <select id='car_transmission' name='car_transmission'>
        <option value='0'> Manuale </option>
        <option value='1'> Automatike </option>
    </select>
    <input type="submit" value="Submit">
</form>
<?php

error_reporting(E_ALL ^ E_NOTICE);

session_start();
require_once 'database.php';
$db = new Database();

session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
}

if (isset($_POST['car_name'])) {
    $car_name = $_POST['car_name'];
    $car_price = $_POST['car_price'];
    $car_desc = $_POST['car_desc'];
    $car_availability = $_POST['car_availability'];
    $car_transmission = $_POST['car_transmission'];

    $sql = "INSERT INTO cars (name, price, description, available, transmission) VALUES ('$car_name', '$car_price', '$car_desc', '$car_availability', '$car_transmission')";
    $result = $db->query($sql);
    if ($result) {

        $image_dir = "../images/car_images/" . $car_name;
        $index_dir = "../assets/car_pages/" . $car_name;

        if (!file_exists($image_dir)) {
            mkdir($image_dir, 0777, true);
        }
        if (!file_exists($index_dir)) {
            mkdir($index_dir, 0777, true);
        }

        echo '<script>alert("Successfully Added")</script>';
        header('Location: index.php');
    } else {
        echo '<script>alert("Car Not Added")</script>';
    }
}
?>

</html>