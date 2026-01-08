<?php

declare(strict_types=1);

namespace Mini\Models;

use Mini\Core\Database;
use Mini\Core\Model;

class User extends Model
{
    private string $nom;
    private string $email;
    private string $password;

    public static function create(array $data): ?self
    {
        $pdo = Database::getPDO();
        
        // Vérif l'email existant ou pa
        if (self::findByEmail($data['email']) !== null) {
            return null;
        }

        $stmt = $pdo->prepare('INSERT INTO user (nom, email, password) VALUES (:nom, :email, :password)');
        $result = $stmt->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);

        if (!$result) {
            return null;
        }

        $user = new self();
        $user->id = (int)$pdo->lastInsertId();
        $user->nom = $data['nom'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->created_at = date('Y-m-d H:i:s');

        return $user;
    }

    public static function findByEmail(string $email): ?self
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new self($row) : null;
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT * FROM user WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new self($row) : null;
    }

    public static function getAll(): array
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->query('SELECT * FROM user');
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => new self($row), $rows);
    }

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->nom = $data['nom'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->password = $data['password'] ?? '';
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    // Obtenir les propriétés c ici pelo
    public function getId(): int { return $this->id ?? 0; }
    public function getEmail(): string { return $this->email; }
    public function getNom(): string { return $this->nom; }
    public function getCreatedAt(): string { return $this->created_at ?? ''; }

    public function update()
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("UPDATE user SET nom = ?, email = ? WHERE id = ?");
        return $stmt->execute([$this->nom, $this->email, $this->id]);
    }




    public function delete()
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
        return $stmt->execute([$this->id]);
    }
}