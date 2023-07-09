<?php
    require_once 'vendor/autoload.php';
    require_once 'Database.php';

    class API {
        private $db;

        function __construct (DB $db) {
            $this->db = $db;
        }

        function checkSid() {
            $sid = session_id();
            $sql = "SELECT * FROM active_sessions WHERE session_id = '$sid'";
            $result = $this->db->execute_query($sql);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return count($data) > 0 ? true : false;
        }

        function fetch_data($property) {

            if (!$this->checkSid()) {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Not permitted'
                ));
            }

            $property_name = $property[0];
            $id = isset($property[1]) ? $property[1] : null;

            $sql = "SELECT * FROM $property_name";
            if ($id) {
                $sql .= " WHERE id = $id";
            }

            $result = $this->db->execute_query($sql);
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $data;
        }
    }