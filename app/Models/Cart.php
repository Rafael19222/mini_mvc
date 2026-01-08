<?php

namespace Mini\Models;

use Mini\Core\Database;
use Mini\Models\Product;

class Cart
{
    private ?int $userId;
    private array $items = [];
    private float $total = 0.0;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId;
        if ($userId) {
            $this->loadFromDatabase();
        }
    }

    public function addItem(int $productId, int $quantity = 1): void
    {
        if (!$this->userId) {
            throw new \Exception('Vous devez être connecté');
        }

        $product = Product::getById($productId);
        if (!$product) {
            throw new \Exception('Produit non trouvé');
        }

        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT id FROM panier WHERE id_user = :user_id AND id_product = :product_id');
        $stmt->execute([':user_id' => $this->userId, ':product_id' => $productId]);
        $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$existing) {
            $stmt = $pdo->prepare('INSERT INTO panier (id_user, id_product) VALUES (:user_id, :product_id)');
            $stmt->execute([':user_id' => $this->userId, ':product_id' => $productId]);
        }

        $this->loadFromDatabase();
    }

    public function removeItem(int $productId): void
    {
        if (!$this->userId) return;

        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('DELETE FROM panier WHERE id_user = :user_id AND id_product = :product_id');
        $stmt->execute([':user_id' => $this->userId, ':product_id' => $productId]);
        $this->loadFromDatabase();
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productId);
        }
    }

    public function clear(): void
    {
        if (!$this->userId) return;
        
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('DELETE FROM panier WHERE id_user = :user_id');
        $stmt->execute([':user_id' => $this->userId]);
        $this->items = [];
        $this->total = 0.0;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItemCount(): int
    {
        return count($this->items);
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    private function loadFromDatabase(): void
    {
        if (!$this->userId) {
            $this->items = [];
            $this->total = 0.0;
            return;
        }

        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT id_product FROM panier WHERE id_user = :user_id');
        $stmt->execute([':user_id' => $this->userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->items = [];
        $this->total = 0.0;

        foreach ($rows as $row) {
            $product = Product::getById((int)$row['id_product']);
            if ($product) {
                $price = $product->getPrice();
                $this->items[(int)$row['id_product']] = [
                    'product' => $product,
                    'quantity' => 1,
                    'price' => $price
                ];
                $this->total += $price;
            }
        }
    }
}