<?php

class Customers {
    private int $id;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $phone_number;
    private string $created_at;

    public function getId(): int {
        return $this->id;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPhoneNumber(): string {
        return $this->phone_number;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPhoneNumber(string $phone_number): void {
        $this->phone_number = $phone_number;
    }

    public function setCreatedAt(string $created_at): void {
        $this->created_at = $created_at;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }
}