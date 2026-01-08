<?php

namespace Mini\Core;

// Modele de base : c'est la classe mère dont vont hériter TOUS les models
// Cette classe n'est pas destinée à être instancié, mais seulement à être héritée
// Elle fournit des méthodes Active Record génériques
abstract class Model
{
    // Ici on veut éviter de répéter les propriétés présentes dans tous les Models
    // On factorise dans la classe "parent" de tous les Models => donc ici meme CoreModel
    // Les propriétés doivent être en protected car on veut pouvoir les utiliser dans les classe enfant (avant ça, elles etaient en private)

    protected $id;
    protected $created_at;
    protected $updated_at;

    /**
     * Nom de la table en base de données
     * Doit être redéfini dans chaque classe enfant
     */
    protected static string $table = '';

    /**
     * Active Record: Récupérer un enregistrement par son ID
     */
    public static function find(int $id): ?static
    {
        $pdo = Database::getPDO();
        $table = static::$table;
        
        if (empty($table)) {
            throw new \Exception('La propriété $table doit être définie dans ' . static::class);
        }

        $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new static($row) : null;
    }

    /**
     * Active Record: Récupérer tous les enregistrements
     */
    public static function all(): array
    {
        $pdo = Database::getPDO();
        $table = static::$table;
        
        if (empty($table)) {
            throw new \Exception('La propriété $table doit être définie dans ' . static::class);
        }

        $stmt = $pdo->query("SELECT * FROM {$table}");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new static($row), $rows);
    }

    /**
     * Active Record: Sauvegarder l'objet (insert ou update)
     * Cette méthode peut être redéfinie dans les classes enfants
     */
    public function save(): bool
    {
        if (isset($this->id) && $this->id > 0) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * Active Record: Insérer un nouvel enregistrement
     * Méthode de base - peut être redéfinie dans les classes enfants
     */
    protected function insert(): bool
    {
        $table = static::$table;
        
        if (empty($table)) {
            throw new \Exception('La propriété $table doit être définie dans ' . static::class);
        }

        // Cette méthode de base doit être redéfinie dans les classes enfants
        // car chaque modèle a des colonnes différentes
        throw new \Exception('La méthode insert() doit être implémentée dans ' . static::class);
    }

    /**
     * Active Record: Mettre à jour un enregistrement existant
     * Méthode de base - peut être redéfinie dans les classes enfants
     */
    protected function update(): bool
    {
        $table = static::$table;
        
        if (empty($table)) {
            throw new \Exception('La propriété $table doit être définie dans ' . static::class);
        }

        // Cette méthode de base doit être redéfinie dans les classes enfants
        // car chaque modèle a des colonnes différentes
        throw new \Exception('La méthode update() doit être implémentée dans ' . static::class);
    }

    /**
     * Active Record: Supprimer un enregistrement
     */
    public function delete(): bool
    {
        if (!isset($this->id) || $this->id <= 0) {
            return false;
        }

        $pdo = Database::getPDO();
        $table = static::$table;
        
        if (empty($table)) {
            throw new \Exception('La propriété $table doit être définie dans ' . static::class);
        }

        $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = :id");
        return $stmt->execute([':id' => $this->id]);
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of updated_at
     */ 
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */ 
    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }
}