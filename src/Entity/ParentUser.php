<?php

namespace App\Entity;

class ParentUser extends User
{
    
    public function createCompteForAdo(): Compte
    {
        return new Compte();
    }

    public function verserHebdo(Ado $ado): void
    {
        $montant = $ado->getArgentHebdo();
        if ($montant > 0) {
            $ado->recevoirArgent($montant);
        }
    }

    public function depot(Ado $ado, int $montant): void
    {
        $ado->recevoirArgent($montant);
    }

    public function enregistrerDepense(Ado $ado, int $montant): void
    {
        $ado->depenserArgent($montant);
    }
}