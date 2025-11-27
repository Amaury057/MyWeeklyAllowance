<?php

namespace Tests\Unit;

use App\Controller\PageController;
use App\Repository\AdoRepository;
use App\Repository\ParentRepository;
use App\Entity\ParentUser;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Entity\Compte;
use App\Entity\Ado;

/**
 * @covers \App\Controller\PageController
 */
class PageControllerTest extends TestCase
{
    private ParentRepository&MockObject $parentRepositoryMock;
    private AdoRepository&MockObject $adoRepositoryMock;
    private PageController $controller;

    protected function setUp(): void
    {
        // Créer des "doublures" (mocks) pour nos dépendances
        $this->parentRepositoryMock = $this->createMock(ParentRepository::class);
        $this->adoRepositoryMock = $this->createMock(AdoRepository::class);

        // Injecter les doublures dans le contrôleur réel
        $this->controller = new PageController(
            $this->parentRepositoryMock,
            $this->adoRepositoryMock
        );
    }

    /**
     * Teste que l'inscription d'un parent réussit quand les données sont valides.
     * @runInSeparateProcess
     */
    public function testRegisterParentSuccess(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'nom' => 'Test Parent',
            'email' => 'test@parent.com',
            'password' => 'secret'
        ];

        // On s'attend à ce que la méthode 'save' du MOCK repository soit appelée.
        $this->parentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ParentUser::class))
            ->willReturn(true);

        // On simule le contrôleur lui-même pour intercepter l'appel à 'redirect'
        $controllerMock = $this->getMockBuilder(PageController::class)
            ->setConstructorArgs([$this->parentRepositoryMock, $this->adoRepositoryMock])
            ->onlyMethods(['redirect'])
            ->getMock();

        // On s'attend à ce que la méthode 'redirect' du MOCK contrôleur soit appelée.
        $controllerMock->expects($this->once())
            ->method('redirect')
            ->with('/dashboard');

        // On lance la méthode à tester sur le MOCK contrôleur
        $controllerMock->registerParent();
    }

    /**
     * Teste que la sauvegarde n'est jamais tentée si les données de l'ado sont invalides.
     * @runInSeparateProcess
     */
    public function testRegisterAdoWithInvalidDataDoesNotSaveAndShowsError(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['nom' => '', 'hebdo' => -10]; // Données invalides

        $this->adoRepositoryMock->expects($this->never())->method('save');

        $controllerMock = $this->getMockBuilder(PageController::class)
            ->setConstructorArgs([$this->parentRepositoryMock, $this->adoRepositoryMock])
            ->onlyMethods(['redirect'])
            ->getMock();

        $controllerMock->expects($this->never())->method('redirect');

        // On capture et ignore la sortie HTML pour supprimer l'avertissement
        ob_start();
        $controllerMock->registerAdo();
        ob_end_clean();
    }

    /**
     * Teste que le dashboard récupère bien les données et les affiche.
     * @runInSeparateProcess
     */
    public function testDashboardFetchesAndDisplaysAdos(): void
    {
        $fakeAdos = [
            ['nom' => 'Ado Un', 'argent_hebdo' => 10, 'compte_id' => 1, 'solde' => 50],
            ['nom' => 'Ado Deux', 'argent_hebdo' => 20, 'compte_id' => null, 'solde' => null]
        ];

        // On configure le MOCK repository pour qu'il retourne nos fausses données
        $this->adoRepositoryMock->expects($this->once())
            ->method('findAllForDashboard')
            ->willReturn($fakeAdos);

        // On capture la sortie HTML pour vérifier son contenu
        ob_start();
        // On exécute la méthode sur le VRAI contrôleur
        $this->controller->dashboard();
        $output = ob_get_clean();

        $this->assertStringContainsString('Ado Un', $output);
        $this->assertStringContainsString('Solde : 50 €', $output);
        $this->assertStringContainsString('Ado Deux', $output);
        $this->assertStringContainsString('Créer un compte', $output);
    }

    /**
     * Teste que la création de compte pour un ado appelle le repository et redirige.
     * @runInSeparateProcess
     */
    public function testCreateCompteForAdoCallsRepositoryAndRedirects(): void
    {
        $_GET['ado_id'] = '42';

        // On s'attend à ce que la méthode 'assignCompteToAdo' soit appelée
        $this->adoRepositoryMock->expects($this->once())
            ->method('assignCompteToAdo')
            ->with($this->isInstanceOf(Compte::class), $this->isInstanceOf(Ado::class));

        // On simule le contrôleur pour intercepter l'appel à 'redirect'
        $controllerMock = $this->getMockBuilder(PageController::class)
            ->setConstructorArgs([$this->parentRepositoryMock, $this->adoRepositoryMock])
            ->onlyMethods(['redirect'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('redirect')
            ->with('/dashboard');

        $controllerMock->createCompteForAdo();
    }


    public function testRegisterParentFailsWhenSaveReturnsFalse(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['nom' => 'Fail Parent', 'email' => 'fail@parent.com', 'password' => 'secret'];

        $this->parentRepositoryMock->method('save')->willReturn(false);

        $controllerMock = $this->getMockBuilder(PageController::class)
            ->setConstructorArgs([$this->parentRepositoryMock, $this->adoRepositoryMock])
            ->onlyMethods(['redirect'])
            ->getMock();

        $controllerMock->expects($this->never())->method('redirect');

        // On capture et ignore la sortie HTML pour supprimer l'avertissement
        ob_start();
        $controllerMock->registerParent();
        ob_end_clean();
    }
}
