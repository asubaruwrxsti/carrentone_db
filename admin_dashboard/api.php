<?php
    session_start();
    header("Location: /admin_dashboard/Login.php");

    require_once 'vendor/autoload.php';
    require_once 'Database.php';

    class API {
        private $db;

        function __construct (DB $db) {
            $this->db = $db;
        }

        function fetch_data($property) {
            $property = $property[0];
            $id = isset($property['id']) ? $property['id'] : null;
        }
    }