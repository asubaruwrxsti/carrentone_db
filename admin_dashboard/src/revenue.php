<?php

class Revenue {
    private int $id;
    private int $customer_id;
    private int $rental_date;
    private int $car_id;
    private int $rental_duration;
    private int $price;

    public function getId(): int {
        return $this->id;
    }

    public function getCustomerId(): int {
        return $this->customer_id;
    }

    public function getRentalDate(): int {
        return $this->rental_date;
    }

    public function getCarId(): int {
        return $this->car_id;
    }

    public function getRentalDuration(): int {
        return $this->rental_duration;
    }

    public function getPrice(): int {
        return $this->price;
    }

    public function setCustomerId(int $customer_id): void {
        $this->customer_id = $customer_id;
    }

    public function setRentalDate(int $rental_date): void {
        $this->rental_date = $rental_date;
    }

    public function setCarId(int $car_id): void {
        $this->car_id = $car_id;
    }

    public function setRentalDuration(int $rental_duration): void {
        $this->rental_duration = $rental_duration;
    }

    public function setPrice(int $price): void {
        $this->price = $price;
    }
}