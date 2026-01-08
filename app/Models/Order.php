<?php

namespace Mini\Models;

use Mini\Core\Database;

class Order
{
    private ?int $id = null;
    private int $userId;
    private array $items;
    private float $total;
    private string $status;
    private string $shippingAddress;
    private string $createdAt;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->userId = $data['userId'] ?? $data['user_id'] ?? 0;
            $this->items = isset($data['items']) ? (is_string($data['items']) ? json_decode($data['items'], true) : $data['items']) : [];
            $this->total = $data['total'] ?? 0.0;
            $this->shippingAddress = $data['shippingAddress'] ?? $data['shipping_address'] ?? '';
            $this->status = $data['status'] ?? self::STATUS_PENDING;
            $this->createdAt = $data['createdAt'] ?? $data['created_at'] ?? date('Y-m-d H:i:s');
        }
    }

    public static function createFromCart(Cart $cart, int $userId, string $shippingAddress): ?Order
    {
        if ($cart->isEmpty()) {
            throw new \Exception('Le panier est vide');
        }

        $order = new self([
            'userId' => $userId,
            'items' => $cart->getItems(),
            'total' => $cart->getTotal(),
            'shippingAddress' => $shippingAddress,
            'status' => self::STATUS_PENDING
        ]);
        
        return $order->save() ? $order : null;
    }

    // Active Record: sauvegarder la commande en base
    public function save(): bool
    {
        $pdo = Database::getPDO();
        
        if (isset($this->id) && $this->id > 0) {
            // Update
            $stmt = $pdo->prepare('UPDATE commande SET user_id = :user_id, items = :items, total = :total, status = :status, shipping_address = :shipping_address WHERE id = :id');
            return $stmt->execute([
                ':user_id' => $this->userId,
                ':items' => json_encode($this->items),
                ':total' => $this->total,
                ':status' => $this->status,
                ':shipping_address' => $this->shippingAddress,
                ':id' => $this->id
            ]);
        } else {
            // Insert
            $stmt = $pdo->prepare('INSERT INTO commande (user_id, items, total, status, shipping_address, created_at) VALUES (:user_id, :items, :total, :status, :shipping_address, NOW())');
            $result = $stmt->execute([
                ':user_id' => $this->userId,
                ':items' => json_encode($this->items),
                ':total' => $this->total,
                ':status' => $this->status,
                ':shipping_address' => $this->shippingAddress
            ]);
            
            if ($result) {
                $this->id = (int)$pdo->lastInsertId();
                $this->createdAt = date('Y-m-d H:i:s');
            }
            
            return $result;
        }
    }

    // Active Record: récupérer une commande par ID
    public static function getById(int $id): ?Order
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT * FROM commande WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? new self([
            'id' => (int)$row['id'],
            'user_id' => (int)$row['user_id'],
            'items' => $row['items'],
            'total' => (float)$row['total'],
            'status' => $row['status'],
            'shipping_address' => $row['shipping_address'],
            'created_at' => $row['created_at']
        ]) : null;
    }

    // Active Record: récupérer toutes les commandes d'un utilisateur
    public static function getByUserId(int $userId): array
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('SELECT * FROM commande WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map(function($row) {
            return new self([
                'id' => (int)$row['id'],
                'user_id' => (int)$row['user_id'],
                'items' => $row['items'],
                'total' => (float)$row['total'],
                'status' => $row['status'],
                'shipping_address' => $row['shipping_address'],
                'created_at' => $row['created_at']
            ]);
        }, $rows);
    }

    // Active Record: supprimer une commande
    public function delete(): bool
    {
        if (!isset($this->id) || $this->id <= 0) {
            return false;
        }

        $pdo = Database::getPDO();
        $stmt = $pdo->prepare('DELETE FROM commande WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    public function getId(): int { return $this->id ?? 0; }
    public function getUserId(): int { return $this->userId; }
    public function getItems(): array { return $this->items; }
    public function getTotal(): float { return $this->total; }
    public function getFormattedTotal(): string { return number_format($this->total, 2, ',', ' ') . ' €'; }
    public function getStatus(): string { return $this->status; }
    public function getShippingAddress(): string { return $this->shippingAddress; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getFormattedCreatedAt(): string { return date('d/m/Y à H:i', strtotime($this->createdAt)); }
    public function getItemCount(): int { return array_sum(array_column($this->items, 'quantity')); }

    public function updateStatus(string $status): void
    {
        $validStatuses = [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_SHIPPED, self::STATUS_DELIVERED, self::STATUS_CANCELLED];
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Statut invalide');
        }
        $this->status = $status;
        $this->save();
    }
}