<!DOCTYPE html>

<style>
    input {
        font-family: Arial,
            Helvetica,
            sans-serif;
        border-collapse: collapse;
        text-align: center;
    }

    td,
    th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }

    th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: center;
        background-color: #04AA6D;
        color: white;
    }

    div {
        border-radius: 5px;
        background-color: #f2f2f2;
        padding: 20px;
    }

    button {
        font-size: 20px;
    }
</style>

<?php
require_once 'database.php';
error_reporting(E_ALL ^ E_NOTICE);

$db = new Database();

session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
}

$car_id = $_GET['id'];

if ($_SESSION['db_connection'] = true) {

    $sql = "SELECT * FROM cars WHERE id = '$car_id'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
?>
    <form action="" method="POST">
        <table border="1" id="cars" style="margin: 0 auto;">
            <tr>
                <td> Emri </td>
                <td> <input type="text" name="car_name" value="<?php echo $row['name']; ?>"> </td>
            </tr>

            <tr>
                <td> Cmimi </td>
                <td> <input type="text" name="car_price" value="<?php echo $row['price']; ?>"> </td>
            </tr>

            <tr>
                <td> Pershkrimi </td>
                <td> <input type="text" name="car_desc" value="<?php echo $row['description']; ?>"> </td>
            </tr>

            <tr>
                <th> <label for='car_transmission'>Kambio:</label></th>
                <td> <select id='car_transmission' name='car_transmission'>
                        <option value='0'> Manuale </option>
                        <option value='1'> Automatike </option>
                    </select> </td>
                <script>
                    document.getElementById("car_transmission").selectedIndex = '<?= $row["transmission"]; ?>';
                </script>
            </tr>

            <tr>
                <th> <label for='car_availability'>Diopozicioni:</label></th>
                <td> <select id='car_availability' name='car_availability'>
                        <option value='0'> Jo Disponibel </option>
                        <option value='1'> Disponibel </option>
                    </select> </td>
                <script>
                    document.getElementById("car_availability").selectedIndex = '<?= $row["available"]; ?>';
                </script>
            </tr>
        </table>

        <div>
            <button type="submit" name="submit">Ruaj</button>
        </div>

    </form>

    <div>
        <button type="submit" name="upload">Shto imazh</button>
    </div>

<?php

    if (isset($_POST['submit'])) {
        $car_name = $_POST['car_name'];
        $car_price = $_POST['car_price'];
        $car_description = $_POST['car_description'];
        $car_transmission = $_POST['car_transmission'];
        $car_availability = $_POST['car_availability'];
        $car_date_taken = $_POST['car_date_taken'];
        $car_date_returned = $_POST['car_date_returned'];
        $car_image = $_POST['car_image'];

        $sql = "UPDATE cars SET price = '$car_price', description = '$car_description', trsnmission = '$car_transmission', availability = '$car_availability', date_taken = '$car_date_taken', date_returned = '$car_date_returned', images = '$car_image' WHERE car_name = '$car_name'";
        $result = $db->query($sql);

        if ($result) {
            echo "Car updated successfully";
            header('Location: admin_dashboard.php');
        } else {
            echo "Error updating car";
        }
    }


    if (isset($_POST['upload'])) {

        $target_dir = "../assets/car_images/" . $car_name . "/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    $images = glob("../assets/car_images/" . $car_name . "/*.jpg");
    foreach ($images as $image) {
        echo "<img src='$image' width='100' height='100' />";
    }
}
