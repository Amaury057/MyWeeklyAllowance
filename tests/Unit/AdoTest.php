<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Ado;
use App\Entity\Compte;

class AdoTest extends TestCase
{
    public function testGetNom(): void
    {
        $ado = new Ado();
        $ado->setNom("John"); // ARRANGE
        $this->assertEquals("John", $ado->getNom()); // ASSERT
    }

    public function testGetCompte(): void
    {
        $ado = new Ado();
        $compte = new Compte();
        $ado->setCompte($compte); // ARRANGE
        $this->assertSame($compte, $ado->getCompte()); // ASSERT
    }

    public function testGetArgentHebdo(): void
    {
        $ado = new Ado();
        $ado->setArgentHebdo(20); // ARRANGE
        $this->assertEquals(20, $ado->getArgentHebdo()); // ASSERT
    }

    public function testRecevoirArgentDelegatesToCompte(): void
    {
        // ARRANGE: Crée un "mock" du Compte pour espionner l'appel de la méthode 'depot'
        $compteMock = $this->createMock(Compte::class);
        $compteMock->expects($this->once())
            ->method('depot')
            ->with(50);

        $ado = new Ado();
        $ado->setCompte($compteMock);

        // ACT
        $ado->recevoirArgent(50);
    }

    public function testRecevoirArgentThrowsExceptionWithoutCompte(): void
    {
        // ASSERT
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("L'ado n'a pas de compte pour recevoir de l'argent.");

        // ARRANGE & ACT
        $ado = new Ado();
        $ado->recevoirArgent(50);
    }

    public function testDepenserArgentDelegatesToCompte(): void
    {
        // ARRANGE
        $compteMock = $this->createMock(Compte::class);
        $compteMock->expects($this->once())
            ->method('retrait')
            ->with(20);

        $ado = new Ado();
        $ado->setCompte($compteMock);

        // ACT
        $ado->depenserArgent(20);
    }

    public function testDepenserArgentThrowsExceptionWithoutCompte(): void
    {
        // ASSERT
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("L'ado n'a pas de compte pour dépenser de l'argent.");

        // ARRANGE & ACT
        $ado = new Ado();
        $ado->depenserArgent(20);
    }
}
