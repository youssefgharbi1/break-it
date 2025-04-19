<?php
namespace App\Model;

class User
{
    private $id;
    private $email;
    private $firstName;
    private $lastName;
    private $password;
    private $phone;
    private $age;
    private $gender;
    private $role;

    public function __construct(
        int $id,
        string $email,
        string $firstName,
        string $lastName,
        string $password,
        ?string $phone = null,
        ?int $age = null,
        ?string $gender = null,
        string $role = 'user'
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
        $this->phone = $phone;
        $this->age = $age;
        $this->gender = $gender;
        $this->role = $role;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getPassword(): string { return $this->password; }
    public function getPhone(): ?string { return $this->phone; }
    public function getAge(): ?int { return $this->age; }
    public function getGender(): ?string { return $this->gender; }
    public function getRole(): string { return $this->role; }
    // Setters
    public function setId(?int $id): void { $this->id = $id; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setFirstName(string $firstName): void { $this->firstName = $firstName; }
    public function setLastName(string $lastName): void { $this->lastName = $lastName; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function setAge(?int $age): void { $this->age = $age; }
    public function setGender(?string $gender): void { $this->gender = $gender; }
    public function setRole(string $role): void { $this->role = $role; }

    // Security method
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
?>