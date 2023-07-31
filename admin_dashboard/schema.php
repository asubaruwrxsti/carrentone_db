<?php
    require_once "database.php";
    require_once "vendor/autoload.php";

    if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
        die("403 - Access Forbidden");
    }

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
         * @return void
         */
        public function createTable($name, $columns) {
            
            $this->dropTable($name);

            $query = "CREATE TABLE IF NOT EXISTS $name (";
            foreach ($columns as $column) {
                $nullValue = $column['Null'] === 'NO' ? 'NOT NULL' : 'NULL';

                if ($column['Default'] !== null && $column['Type'] !== "varchar(255)") {
                    $defaultValue = "DEFAULT {$column['Default']}";
                } else if ($column['Type'] == "varchar(255)") {
                    $defaultValue = "DEFAULT '{$column['Default']}'";
                } else {
                    $defaultValue = '';
                }

                $query .= "{$column['Field']} {$column['Type']} {$nullValue} {$defaultValue} {$column['Extra']},";
                if ($column['Key'] == "PRI") {
                    $query .= "PRIMARY KEY ({$column['Field']}),";
                }
            }
            $query = rtrim($query, ',');
            $query .= ");";
            $this->db->execute_query($query);
        }
        

        /**
         * Drop a table from the database
         * @return void
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
            $schema = json_decode(json_encode($this->schema), true);

            foreach ($schema as $table => $columns) {
                try {
                    $res = $this->db->execute_query("SHOW COLUMNS FROM $table");
                    $db_columns = $res->fetch_all(MYSQLI_ASSOC);
                    if ($columns != $db_columns) {
                        return false;
                    }
                } catch (Exception $e) {
                    return false;
                }
            }
            return true;
        }

        /**
         * Restore the database from the local schema
         * @return boolean
         */
        public function restoreSchema() {
            $schema = json_decode(json_encode($this->schema), true);
            foreach ($schema as $table => $columns) {
                try {
                    $res = $this->db->execute_query("SHOW COLUMNS FROM $table");
                    $db_columns = $res->fetch_all(MYSQLI_ASSOC);
                    if ($columns != $db_columns) {
                        $this->dropTable($table);
                        $this->createTable($table, $columns);
                    }
                } catch (Exception $e) {
                    $this->createTable($table, $columns);
                }
            }
        }

        /**
         * Import a schema into the database
         * @return boolean
         */
        public function importSchema($schema) {
            $this->schema = $schema;
            $this->restoreSchema();
        }

        /**
         * Run a changeset on the database
         * @return boolean
         */
        public function runChangeset($changeSet) {
            require_once "{$changeSet['path']}";
            $changeSet['class']::{$changeSet['method']}($this);

            $this->saveSchema();
            return $this->verifySchema();
        }
    }