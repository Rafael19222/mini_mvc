<?php

declare(strict_types=1);

namespace Mini\Models;

use Mini\Core\Model;
use Mini\Core\Database;

class Product extends Model
{
    // Database-backed implementation

    public static function getAll(): array
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->query('SELECT id, nom, prix, description, image FROM produit ORDER BY id DESC');
        $rows = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        return array_map(fn($r) => new self(self::mapRow($r)), $rows);
    }

    public static function getById(int $id): ?self
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT id, nom, prix, description, image FROM produit WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new self(self::mapRow($row)) : null;
    }

    public static function getFeatured(): array
    {
        // If no featured column, just return latest 4 items
        $pdo = Database::getPDO();
        $stmt = $pdo->query('SELECT id, nom, prix, description, image FROM produit ORDER BY id DESC LIMIT 4');
        $rows = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        return array_map(fn($r) => new self(self::mapRow($r)), $rows);
    }

    public static function getByCategory(string $category): array
    {
        // No category column in DB screenshot; return all for now
        return self::getAll();
    }

    public static function search(string $query): array
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT id, nom, prix, description, image FROM produit 
                                WHERE nom LIKE :q OR description LIKE :q 
                                ORDER BY id DESC');
        $stmt->execute([':q' => '%' . $query . '%']);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r) => new self(self::mapRow($r)), $rows);
    }

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->description = $data['description'];
            $this->price = $data['price'];
            $this->image = $data['image'];
            $this->category = $data['category'] ?? 'Divers';
        }
    }

    private static function mapRow(array $row): array
    {
        return [
            'id' => (int)$row['id'],
            'name' => $row['nom'] ?? ($row['name'] ?? ''),
            'description' => $row['description'] ?? '',
            'price' => isset($row['prix']) ? (float)$row['prix'] : (float)($row['price'] ?? 0),
            'image' => $row['image'] ?? '',
            'category' => $row['category'] ?? 'Divers',
        ];
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getPrice(): float { return $this->price; }
    public function getImage(): string { return $this->image; }
    public function getCategory(): string { return $this->category ?? 'Divers'; }
    public function getStock(): int { return (int)($this->stock ?? 0); }
    public function isFeatured(): bool { return (bool)($this->featured ?? false); }
    public function getCreatedAt(): string { return $this->created_at; }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function getCategoryName(): string
    {
        $categories = [
            'pokemon' => 'Pokémon',
            'one-piece' => 'One Piece',
            'lorcana' => 'Disney Lorcana'
        ];
        
        return $categories[$this->category] ?? $this->category;
    }

    // Active Record: méthode pour sauvegarder (insert ou update)
    public function save(): bool
    {
        if (isset($this->id) && $this->id > 0) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    // Active Record: insérer un nouveau produit
    protected function insert(): bool
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('INSERT INTO produit (nom, prix, description, image) VALUES (:nom, :prix, :description, :image)');
        $result = $stmt->execute([
            ':nom' => $this->name,
            ':prix' => $this->price,
            ':description' => $this->description,
            ':image' => $this->image
        ]);

        if ($result) {
            $this->id = (int)$pdo->lastInsertId();
        }

        return $result;
    }

    // Active Record: mettre à jour un produit existant
    public function update(): bool
    {
        if (!isset($this->id) || $this->id <= 0) {
            return false;
        }

        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('UPDATE produit SET nom = :nom, prix = :prix, description = :description, image = :image WHERE id = :id');
        return $stmt->execute([
            ':nom' => $this->name,
            ':prix' => $this->price,
            ':description' => $this->description,
            ':image' => $this->image,
            ':id' => $this->id
        ]);
    }

    // Active Record: supprimer un produit
    public function delete(): bool
    {
        if (!isset($this->id) || $this->id <= 0) {
            return false;
        }

        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('DELETE FROM produit WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }
}