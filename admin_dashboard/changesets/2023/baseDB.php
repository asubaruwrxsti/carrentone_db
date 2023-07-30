<?php
    require_once "vendor/autoload.php";
    require_once "database.php";
    require_once "schema.php";

    if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
        die("403 - Access Forbidden");
    }
    
    class baseDB extends Schema {
        private $schema;

        public function __construct() {
            $this->schema = parent::saveSchema();
            $this->dumpSchema();
            parent::__construct();

            // Sample what a changeset might look like
            $changeSet = [
                'table' => 'users',
                'columns' => [
                    [
                        'Field' => 'id',
                        'Type' => 'int(11)',
                        'Null' => 'NO',
                        'Key' => 'PRI',
                        'Default' => null,
                        'Extra' => 'auto_increment'
                    ]
                ]
            ];
        }

        public function runChangeset($changeSet) {
            $this->createTable($changeSet['table'], $changeSet['columns']);
        }

        public function revertChangeset($changeSet) {
            $this->dropTable($changeSet['table']);
            $this->restoreSchema();
        }
    }