<?php

namespace App\Repository;

use App\Entity\Ado;
use App\Entity\Compte;
use PDO;

class AdoRepository
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Ado $ado): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO ados (nom, argent_hebdo) VALUES (:nom, :argent_hebdo)"
        );
        $success = $stmt->execute([
            'nom' => $ado->getNom(),
            'argent_hebdo' => $ado->getArgentHebdo(),
        ]);

        if ($success) {
            $ado->setId($this->pdo->lastInsertId());
        }

        return $success;
    }

    /**
     * Ajoute un montant positif au solde d'un compte existant.
     */
    public function addMoneyToCompte(int $compteId, int $montant): bool
    {
        if ($montant <= 0) {
            return false; // On ne gère que les dépôts positifs
        }

        $stmt = $this->pdo->prepare(
            "UPDATE comptes SET solde = solde + :montant WHERE id = :compte_id"
        );

        return $stmt->execute([
            'montant' => $montant,
            'compte_id' => $compteId
        ]);
    }

    public function updateArgentHebdo(int $adoId, int $montant): bool
    {
        if ($montant < 0) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE ados SET argent_hebdo = :montant WHERE id = :ado_id"
        );

        return $stmt->execute([
            'montant' => $montant,
            'ado_id' => $adoId
        ]);
    }

    /**
     * Vérifie si un ado possède un compte.
     */
    public function hasCompte(int $adoId): bool
    {
        $stmt = $this->pdo->prepare("SELECT compte_id FROM ados WHERE id = :ado_id");
        $stmt->execute(['ado_id' => $adoId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result && $result['compte_id'] !== null;
    }

    public function assignCompteToAdo(Compte $compte, Ado $ado): bool
    {
        $this->pdo->beginTransaction();
        try {
            // 1. Sauvegarder le compte
            $stmtCompte = $this->pdo->prepare("INSERT INTO comptes (solde) VALUES (:solde)");
            $stmtCompte->execute(['solde' => $compte->getSolde()]);
            $compteId = $this->pdo->lastInsertId();
            $compte->setId($compteId);

            // 2. Mettre à jour l'ado avec l'ID du nouveau compte
            $stmtAdo = $this->pdo->prepare("UPDATE ados SET compte_id = :compte_id WHERE id = :ado_id");
            $stmtAdo->execute(['compte_id' => $compteId, 'ado_id' => $ado->getId()]);

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function findAllForDashboard(): array
    {
        $stmt = $this->pdo->query(
            "SELECT 
                a.id, a.nom, a.argent_hebdo, a.compte_id,
                c.solde 
            FROM ados a
            LEFT JOIN comptes c ON a.compte_id = c.id
            ORDER BY a.created_at DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
