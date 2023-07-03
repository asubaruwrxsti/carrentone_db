<?php

    class DB {
        function __construct($hostname, $username, $password, $database) {
            $this->create_db_conn($hostname, $username, $password, $database);
        }

        function create_db_conn($hostname, $username, $password, $database) {
            $conn = mysqli_connect($hostname, $username, $password, $database);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            return $conn;
        }

        function close_connection($conn) {
            mysqli_close($conn);
        }
    }

?>