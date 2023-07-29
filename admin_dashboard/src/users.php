<?php

class Users {
    private int $id;
    private string $username;
    private string $password;
    private string $date_created;
    private string $currency;
    private string $last_login;
    private string $last_ip;
    private int $is_admin;

    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getDateCreated(): string {
        return $this->date_created;
    }

    public function getCurrency(): string {
        return $this->currency;
    }

    public function getLastLogin(): string {
        return $this->last_login;
    }

    public function getLastIp(): string {
        return $this->last_ip;
    }

    public function getIsAdmin(): int {
        return $this->is_admin;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function setDateCreated(string $date_created): void {
        $this->date_created = $date_created;
    }

    public function setCurrency(string $currency): void {
        $this->currency = $currency;
    }

    public function setLastLogin(string $last_login): void {
        $this->last_login = $last_login;
    }

    public function setLastIp(string $last_ip): void {
        $this->last_ip = $last_ip;
    }

    public function setIsAdmin(int $is_admin): void {
        $this->is_admin = $is_admin;
    }
}