<?php

namespace App\Entity;

class Ado extends User
{
    private ?Compte $compte = null; 
    private int $argentHebdo = 0;

    public function getCompte(): ?Compte 
    {
        return $this->compte;
    }

    public function setCompte(Compte $compte): self
    {
        $this->compte = $compte;
        return $this;
    }

    public function getArgentHebdo(): int
    {
        return $this->argentHebdo;
    }

    public function setArgentHebdo(int $argentHebdo): self
    {
        $this->argentHebdo = $argentHebdo;
        return $this;
    }

    public function recevoirArgent(int $montant): void
    {
        if (!$this->compte) {
            throw new \Exception("L'ado n'a pas de compte pour recevoir de l'argent.");
        }
        $this->compte->depot($montant);
    }

    public function depenserArgent(int $montant): void
    {
        if (!$this->compte) {
            throw new \Exception("L'ado n'a pas de compte pour dépenser de l'argent.");
        }
        $this->compte->retrait($montant);
    }
}
