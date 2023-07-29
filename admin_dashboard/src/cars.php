<?php

class Cars {
    private int $id;
    private string $name;
    private int $price;
    private string $description;
    private int $travel_distance;
    private int $transmission;
    private int $available;
    private string $next_order;
    private string $order_count;
    private string $created_at;

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPrice(): int {
        return $this->price;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getTravelDistance(): int {
        return $this->travel_distance;
    }

    public function getTransmission(): int {
        return $this->transmission;
    }

    public function getAvailable(): int {
        return $this->available;
    }

    public function getNextOrder(): string {
        return $this->next_order;
    }

    public function getOrderCount(): string {
        return $this->order_count;
    }

    public function getCreatedAt(): string {
        return $this->created_at;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setPrice(int $price): void {
        $this->price = $price;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setTravelDistance(int $travel_distance): void {
        $this->travel_distance = $travel_distance;
    }

    public function setTransmission(int $transmission): void {
        $this->transmission = $transmission;
    }

    public function setAvailable(int $available): void {
        $this->available = $available;
    }

    public function setNextOrder(string $next_order): void {
        $this->next_order = $next_order;
    }

    public function setOrderCount(string $order_count): void {
        $this->order_count = $order_count;
    }

    public function setCreatedAt(string $created_at): void {
        $this->created_at = $created_at;
    }
}