<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Compte;
use App\Exception\InvalidArgumentException;

class CompteTest extends TestCase
{
    public function testSoldeNewAccount(): void
    {
        $compte = new Compte();
        $this->assertEquals(0, $compte->getSolde());
    }

    public function testDepot(): void
    {
        $compte = new Compte();
        $compte->depot(100);
        $this->assertEquals(100, $compte->getSolde());
    }

    public function testRetrait(): void
    {
        $compte = new Compte();
        $compte->depot(200);
        $compte->retrait(50);
        $this->assertEquals(150, $compte->getSolde());
    }

    public function testRetraitInsuffisant(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $compte = new Compte();
        $compte->depot(100);
        $compte->retrait(150);
    }

    public function testDepotNegatif(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $compte = new Compte();
        $compte->depot(-50);
    }   

    public function testRetraitNegatif(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $compte = new Compte();
        $compte->depot(100);
        $compte->retrait(-30);
    }

    public function testSetAndGetId(): void
    {
        $compte = new Compte();
        $compte->setId(5);
        $this->assertEquals(5, $compte->getId());
    }

    public function testSetAndGetSolde(): void
    {
        $compte = new Compte();
        $compte->setSolde(250);
        $this->assertEquals(250, $compte->getSolde());
    }
}   