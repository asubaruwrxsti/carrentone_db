<?php

class Messages {
    private int $id;
    private int $data;

    public function getId(): int {
        return $this->id;
    }

    public function getData(): int {
        return $this->data;
    }

    public function setData(int $data): void {
        $this->data = $data;
    }
}