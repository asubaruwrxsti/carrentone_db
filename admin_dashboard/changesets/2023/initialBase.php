<?php
    require_once "schema.php";
    require_once "vendor/autoload.php";

    class Base extends Schema {
        public function __construct() {
            parent::__construct();
        }

        public function createTables() {
            $this->createTable("users", [
                [
                    "name" => "id",
                    "type" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                    "extra" => ""
                ],
                [
                    "name" => "username",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "password",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "date_created",
                    "type" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                    "extra" => ""
                ],
                [
                    "name" => "currency",
                    "type" => "VARCHAR(255)",
                    "extra" => "NULL"
                ],
                [
                    "name" => "last_login",
                    "type" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                    "extra" => ""
                ],
                [
                    "name" => "last_ip",
                    "type" => "VARCHAR(255)",
                    "extra" => "NULL"
                ],
                [
                    "name" => "is_admin",
                    "type" => "TINYINT(1)",
                    "extra" => "NOT NULL DEFAULT 0"
                ],
            ]);

            $this->createTable("revenue",[
                [
                    "name" => "id",
                    "type" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                    "extra" => ""
                ],
                [
                    "name" => "customer_id",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "rental_date",
                    "type" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                    "extra" => ""
                ],
                [
                    "name" => "car_id",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "rental_duration",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "price",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL"
                ],
            ]);

            $this->createTable("messages",[
                [
                    "name" => "id",
                    "type" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                    "extra" => ""
                ],
                [
                    "name" => "data",
                    "type" => "TEXT",
                    "extra" => "NOT NULL"
                ],
            ]);

            $this->createTable("customers",[
                [
                    "name" => "id",
                    "type" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                    "extra" => ""
                ],
                [
                    "name" => "firstname",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "lastname",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "email",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "phone_number",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "created_at",
                    "type" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                    "extra" => ""
                ],
            ]);

            $this->createTable("cars",[
                [
                    "name" => "id",
                    "type" => "INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY",
                    "extra" => ""
                ],
                [
                    "name" => "name",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "price",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "description",
                    "type" => "TEXT",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "travel_distance",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "transmission",
                    "type" => "TINYINT(1)",
                    "extra" => "NOT NULL DEFAULT 0"
                ],
                [
                    "name" => "available",
                    "type" => "TINYINT(1)",
                    "extra" => "NOT NULL DEFAULT 1"
                ],
                [
                    "name" => "next_order",
                    "type" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                    "extra" => ""
                ],
                [
                    "name" => "order_count",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL DEFAULT 0"
                ],
                [
                    "name" => "created_at",
                    "type" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                    "extra" => ""
                ],
            ]);

            $this->createTable("active_sessions",[
                [
                    "name" => "uid",
                    "type" => "INT(11) UNSIGNED",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "session_id",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
                [
                    "name" => "ip_address",
                    "type" => "VARCHAR(255)",
                    "extra" => "NOT NULL"
                ],
            ]);
        }
    }