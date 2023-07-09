<?php
    require_once 'vendor/autoload.php';
    class DB {
        private $conn;
        private $purifier_config;
        private $purifier;

        function __construct($hostname, $username, $password, $database) {
            $this->create_db_conn($hostname, $username, $password, $database);
            $this->purifier_config = HTMLPurifier_Config::createDefault();
            $this->purifier = new HTMLPurifier($this->purifier_config);
        }

        function create_db_conn($hostname, $username, $password, $database) {
            $this->conn = mysqli_connect($hostname, $username, $password, $database);
            if (!$this->conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            return $this->conn;
        }

        function execute_query($sql) {
            $sql = $this->purifier->purify($sql);
            $result = mysqli_query($this->conn, $sql);
            return $result;
        }

        function close_connection() {
            mysqli_close($this->conn);
        }

        function create_session_id($uid) {
            $this->conn->execute_query(sprintf("UPDATE users SET last_login = NOW() WHERE id = %d", $uid));
            $this->conn->execute_query(sprintf("INSERT INTO `active_sessions` (uid, session_id) VALUES (%d, '%s')", $uid, session_id()));
        }

        function destroy_session_id($uid) {
            $this->conn->execute_query(sprintf("DELETE FROM `active_sessions` WHERE uid = %d", $uid));
        }

    }

?>