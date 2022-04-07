
<?php

session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
}

class Database
{

    public function __construct()
    {
        $this->connection = new mysqli("localhost", "root", "", "carrentone");
        if ($this->connection->connect_error) {
            echo("Error: " . $this->connection->connect_error);
            exit();
        }
        $_SESSION['db_connection'] = true;
    }

    public function query($sql)
    {
        $result = $this->connection->query($sql);
        return $result;
    }

    public function close()
    {
        $this->connection->close();
    }

}
