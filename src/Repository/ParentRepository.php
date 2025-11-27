<?php

namespace App\Repository;

use App\Entity\ParentUser;
use PDO;

class ParentRepository
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(ParentUser $parent): bool
    {
        if (null === $parent->getEmail() || null === $parent->getPassword()) {
            return false;
        }

        $hashedPassword = password_hash($parent->getPassword(), PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO parents (nom, email, password) VALUES (:nom, :email, :password)"
        );
        
        $success = $stmt->execute([
            'nom' => $parent->getNom(),
            'email' => $parent->getEmail(),
            'password' => $hashedPassword
        ]);

        if ($success) {
            $parent->setId($this->pdo->lastInsertId());
        }

        return $success;
    }
}