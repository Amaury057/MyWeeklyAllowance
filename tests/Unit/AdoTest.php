<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Ado;
use App\Compte;

class AdoTest extends TestCase
{
    public function testGetnom(): void
    {
        $compte = new Compte();
        $ado = new Ado("Thomas", $compte);
        
        $this->assertEquals("Thomas", $ado->getNom());
    }

    public function testGetCompte(): void
    {
        $compte = new Compte();
        $ado = new Ado("Thomas", $compte);
        
        $this->assertSame($compte, $ado->getCompte());
    }

    public function testGetargentHebdo(): void
    {
        $compte = new Compte();
        $ado = new Ado("Thomas", $compte, 20);
        
        $this->assertEquals(20, $ado->getArgentHebdo());
    }

}

?>