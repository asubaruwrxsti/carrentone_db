<?php

class ActiveSession {
    private int $uid;
    private string $session_id;
    private string $ip_address;

    public function getSessionId(): string {
        return $this->session_id;
    }

    public function getUid(): int {
        return $this->uid;
    }

    public function getIpAddress(): string {
        return $this->ip_address;
    }

    public function setSessionId(string $session_id): void {
        $this->session_id = $session_id;
    }

    public function setUid(int $uid): void {
        $this->uid = $uid;
    }

    public function setIpAddress(string $ip_address): void {
        $this->ip_address = $ip_address;
    }
}