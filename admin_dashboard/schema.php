#!/usr/bin/env php
<?php
    require_once "database.php";
    require_once "vendor/autoload.php";

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    /**
     * Schema class
     * @package admin_dashboard
     */
    class Schema {
        private $tables = [];
        private $db;
        private $schema;

        public function __construct() {
            $this->db = new DB($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
            try {
                $this->schema = json_decode(file_get_contents("schema.json"), true);
            } catch (Exception $e) {
                $this->schema = '';
            }
        }

        /**
         * Get the value of tables
         * @return array
         */
        public function getSchema() {
            return $this->schema;
        }

        /**
         * Dump the value of tables in JSON format
         * @param array $schema
         */
        public function dumpSchema() {
            file_put_contents("schema.json", $this->schema);
        }

        /**
         * Create a table in the database
         * @return array
         */
        public function createTable($name, $columns) {
            $query = "CREATE TABLE IF NOT EXISTS $name (";
            foreach ($columns as $column) {
                $query .= $column['name'] . " " . $column['type'] . " " . $column['extra'] . ",";
            }
            $query = rtrim($query, ",");
            $query .= ");";
            $this->db->execute_query($query);
            $this->tables[$name] = $columns;
        }

        /**
         * Drop a table from the database
         * @return array
         */
        public function dropTable($name) {
            $this->db->execute_query("DROP TABLE IF EXISTS $name");
        }

        /**
         * Save the schema of the database in JSON format
         * @return array
         */
        public function saveSchema() {
            $schema = [];
            foreach ($this->tables as $table) {
                $res = $this->db->execute_query("SHOW COLUMNS FROM {$table['Tables_in_' . $_ENV['DB_NAME']]}");
                $schema[$table['Tables_in_' . $_ENV['DB_NAME']]] = $res->fetch_all(MYSQLI_ASSOC);
            }

            $this->schema = json_encode($schema, JSON_PRETTY_PRINT);
            return $this->schema;
        }

        /**
         * Check if the local schema matches the database schema
         * @return boolean
         */
        public function verifySchema() {
            $schema = json_decode($this->schema, true);
            foreach ($schema as $table => $columns) {
                $res = $this->db->execute_query("SHOW COLUMNS FROM $table");
                $db_columns = $res->fetch_all(MYSQLI_ASSOC);
                if ($columns != $db_columns) {
                    return false;
                }
            }
            return true;
        } 

        /**
         * Import a schema into the database
         * @return array
         */
        public function importSchema($schema) {

        }

        /**
         * Update the schema of the database
         * @return array
         */
        public function updateSchema() {

        }
    }