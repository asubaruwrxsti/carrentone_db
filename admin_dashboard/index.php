<!DOCTYPE html>
<style>
    #cars {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 80%;
        text-align: center;
    }

    #cars td,
    #cars th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #cars tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #cars tr:hover {
        background-color: #ddd;
    }

    #cars th {
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

<body>

    <br>

    <table border="1" id="cars" style="margin: 0 auto;">
        <tr>
            <th>Emer</th>
            <th>Cmim</th>
            <th>Pershkrim</th>
            <th>Disponibel</th>
            <th>Veprime</th>
        </tr>

        <?php
        require_once 'database.php';
        error_reporting(E_ALL ^ E_NOTICE);

        session_start();
        if (!isset($_SESSION['logged_in'])) {
            header('Location: login.php');
        }

        $db = new Database();
        $sql = "SELECT * FROM cars";
        $result = $db->query($sql);


        foreach ($result as $k => $v) {
        ?>
            <tr>
                <td><?php echo $v['name']; ?></td>
                <td><?php echo $v['price']; ?> Euro</td>
                <td><?php echo $v['description']; ?></td>
                <td><?php if ($v['available'] == 1) {
                        echo "Po";
                    } else {
                        echo "Jo";
                    } ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $v['id']; ?>"><button>Edit</button></a>
                    <a href="delete.php?id=<?php echo $v['id']; ?>"><button>Delete</button></a>
                </td>
            </tr>
        <?php
        }
        ?>

    </table> <br> <br>

    <center>
        <a href="add.php"><button class="btn btn-primary btn-lg" style="background-color: #04AA6D; color: white;">Add Car</button></a>
        <a href="logout.php"><button class="btn btn-primary btn-lg" style="background-color: #04AA6D; color: white;">Logout</button></a>
    </center>

</body>