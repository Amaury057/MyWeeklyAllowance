<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\ParentUser;
use App\Compte;
use App\Ado;

class ParentTest extends TestCase
{
    public function testParentArgentHebdo(): void
    {
        $compte = new Compte();
        $ado = new Ado("Thomas", $compte, 20);
        $parent = new ParentUser();
        $parent->VerserHebdo($ado);
        $this->assertEquals(20, $compte->getSolde());
    }

    public function testParentCreateCompteForAdo(): void
    {
        $parent = new ParentUser();
        $compte = $parent->createCompteForAdo();
        $this->assertInstanceOf(Compte::class, $compte);
        $this->assertEquals(0, $compte->getSolde());
    }

    public function testParentDepot(): void
    {
        $compte = new Compte();
        $ado = new Ado("Thomas", $compte); 
        $parent = new ParentUser();
        
        $parent->depot($ado, 50);

        $this->assertEquals(50, $compte->getSolde());
    }

    public function testParentPeutEnregistrerUneDepense(): void
    {
        
        $compte = new Compte();
        $ado = new Ado("Thomas", $compte); 
        $parent = new ParentUser();
        $compte->depot(50); 

        $parent->enregistrerDepense($ado, 15);

        $this->assertEquals(35, $compte->getSolde());
    }



}



?>