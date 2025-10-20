<?php

namespace App\Dominio;

class UsuarioDominio {
    
    private string $email;
    private string $password;
    private bool $status;
    private int $failedAttempts;
    private ?string $lockUntil;
    
    public function __construct(string $email, string $password, bool $status, int $failedAttempts, ?string $lockUntil) {
        $this->email = $email;
        $this->password = $password;
        $this->status = $status;
        $this->failedAttempts = $failedAttempts;
        $this->lockUntil = $lockUntil;
    }

    public function getEmail(): string {
        return $this->email;
    }
    public function setEmail(string $email): void {
        $this->email = $email;
    }
    public function getPassword(): string {
        return $this->password;
    }
    public function setPassword(string $password): void {
        $this->password = $password;
    }
    public function isStatus(): bool {
        return $this->status;
    }
    public function setStatus(bool $status): void {
        $this->status = $status;
    }
    public function getFailedAttempts(): int {
        return $this->failedAttempts;
    }
    public function setFailedAttempts(int $failedAttempts): void {
        $this->failedAttempts = $failedAttempts;
    }
    public function getLockUntil(): ?string {
        return $this->lockUntil;
    }
    public function setLockUntil(?string $lockUntil): void {
        $this->lockUntil = $lockUntil;
    }
}