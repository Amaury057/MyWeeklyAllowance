<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\ParentUser;
use App\Entity\Compte;
use App\Entity\Ado;

class ParentTest extends TestCase
{
    public function testParentArgentHebdo(): void
    {
        $parent = new ParentUser();
        $ado = new Ado();
        $ado->setArgentHebdo(20); // ARRANGE: Définir l'argent hebdo
        $ado->setCompte(new Compte()); // ARRANGE: Donner un compte à l'ado

        $parent->verserHebdo($ado); // ACT
        $this->assertEquals(20, $ado->getCompte()->getSolde()); // ASSERT
    }

    public function testCreateCompteForAdo(): void
    {
        $parent = new ParentUser();
        $this->assertInstanceOf(Compte::class, $parent->createCompteForAdo());
    }

    public function testParentDepot(): void
    {
        $parent = new ParentUser();
        $ado = new Ado();
        $ado->setCompte(new Compte()); // ARRANGE: Donner un compte à l'ado
        $ado->getCompte()->setSolde(10);

        $parent->depot($ado, 50); // ACT
        $this->assertEquals(60, $ado->getCompte()->getSolde()); // ASSERT
    }

    public function testParentPeutEnregistrerUneDepense(): void
    {
        $parent = new ParentUser();
        $ado = new Ado();
        $ado->setCompte(new Compte()); // ARRANGE: Donner un compte à l'ado
        $ado->getCompte()->setSolde(100);

        $parent->enregistrerDepense($ado, 20); // ACT
        $this->assertEquals(80, $ado->getCompte()->getSolde()); // ASSERT
    }

    public function testParentEnregistrerDepenseInsuffisante(): void
    {
        $this->expectException(\InvalidArgumentException::class); // ASSERT: on attend une exception
        $parent = new ParentUser();
        $ado = new Ado();
        $ado->setCompte(new Compte()); // ARRANGE
        $ado->getCompte()->setSolde(10);

        $parent->enregistrerDepense($ado, 20); // ACT
    }

    public function testParentEnregistrerDepenseNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class); // ASSERT
        $parent = new ParentUser();
        $ado = new Ado();
        $ado->setCompte(new Compte()); // ARRANGE
        $ado->getCompte()->setSolde(100);

        $parent->enregistrerDepense($ado, -20); // ACT
    }
}
