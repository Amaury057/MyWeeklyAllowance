<?php

namespace App\Entity;

class Compte
{
    private ?int $id = null;
    private int $solde = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getSolde(): int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;
        return $this;
    }

    public function depot(int $montant): void
    {
        if ($montant <= 0) {
            throw new \InvalidArgumentException("Le montant du dépôt doit être positif.");
        }
        $this->solde += $montant;
    }

    public function retrait(int $montant): void
    {
        if ($montant <= 0) {
            throw new \InvalidArgumentException("Le montant du retrait doit être positif.");
        }
        if ($this->solde < $montant) {
            throw new \InvalidArgumentException("Solde insuffisant.");
        }
        $this->solde -= $montant;
    }
}