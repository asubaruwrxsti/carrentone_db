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
                <td> <input type="text" name="car_description" value="<?php echo $row['description']; ?>"> </td>
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
        <form action="" method="POST" enctype="multipart/form-data">
            Select image to upload:
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload Image" name="upload">
    </div>

<?php


    $images = glob("../images/car_images/" . $row['name'] . "/*.jpg");
    foreach ($images as $image) {
        echo "<img src='$image' />";
    }


    if (isset($_POST['submit'])) {
        $car_name = $_POST['car_name'];
        $car_price = $_POST['car_price'];
        $car_description = $_POST['car_description'];
        $car_transmission = $_POST['car_transmission'];
        $car_availability = $_POST['car_availability'];

        $sql = "UPDATE cars SET price = '$car_price', description = '$car_description', transmission = '$car_transmission', available = '$car_availability' WHERE name = '$car_name' AND id = '$car_id'";
        echo $sql;
        $res = $db->query($sql);

        if ($res) {

            $file = fopen("../assets/car_info/template.txt", "r");
            $template = fread($file, filesize("../assets/car_info/template.txt"));

            $template = str_replace("{car_name}", $car_name, $template);
            $template = str_replace("{car_price}", $car_price." Euro", $template);
            $template = str_replace("{car_description}", $car_description, $template);
            if ($car_transmission == 1) {
                $template = str_replace("{car_transmission}", "Automatic", $template);
            } else {
                $template = str_replace("{car_transmission}", "Manual", $template);
            }
            if ($car_availability == 1) {
                $template = str_replace("{car_availability}", "Available", $template);
            } else {
                $template = str_replace("{car_availability}", "Not Available", $template);
            }

            $img_src = '';
            foreach ($images as $image) {
                $image = substr($image, 2);
                $img_src .= '<div class="col-sm-12 col-md-4 col-lg-4">
                <a class="lightbox">
                    <img class="img-fluid" src="' . $image . '">
                </a>
            </div>';
            }

            $template = str_replace("{car_reference}", $img_src, $template);

            $file = fopen("../assets/car_pages/" . $car_name . "/index.html", "w");
            fwrite($file, $template);
            fclose($file);

            $file = fopen("../assets/car_info/cars.txt", "r");
            $cars = fread($file, filesize("../assets/car_info/cars.txt"));

            $sql = "SELECT * FROM cars";
            $result = $db->query($sql);
            $car_special = '';
            while ($row = $result->fetch_assoc()) {

                $images = glob("../images/car_images/" . $row['name'] . "/*.jpg");
                $image = $images[0];
                $image = substr($image, 2);

                if ($row['available'] == 0) {
                    $car_special .= '<div class="col-lg-4 col-md-6 special-grid ' . $row['transmission'] . '">
            <div class="gallery-single fix">
                <a href="/assets/car_pages/' . $row['name'] . '/index.html" target="_blank">
                    <img src="' . $image . '" class="img-fluid" alt="Image">
                    <div class="why-text">
                        <h4>' . $row['price'] . ' Euro </h4>
                        <p>' . $row['description'] . '</p>
                        <h5>Unavailable</h5>
                    </div>
            </div>
        </div>';
                } else {
                    $car_special .= '<div class="col-lg-4 col-md-6 special-grid ' . $row['transmission'] . '">
            <div class="gallery-single fix">
                <a href="/assets/car_pages/' . $row['name'] . '/index.html" target="_blank">
                    <img src="' . $image . '" class="img-fluid" alt="Image">
                    <div class="why-text">
                        <h4>' . $row['price'] . ' Euro </h4>
                        <p>' . $row['description'] . '</p>
                        <h5>Availabe</h5>
                    </div>
            </div>
        </div>';
                }
            }

            $cars = str_replace("{special_list}", $car_special, $cars);

            $file = fopen("../cars.html", "w");
            fwrite($file, $cars);
            fclose($file);


            header('Location: index.php');
        } else {
            echo "Error updating car";
        }
    }


    if (isset($_POST['upload'])) {

        $target_dir = "../images/car_images/" . $row['name'] . "/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

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
}
