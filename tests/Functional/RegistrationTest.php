<?php

namespace Tests\Functional;

use App\Controller\PageController;
use App\Repository\AdoRepository;
use App\Repository\ParentRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
{
    private ?PDO $pdo = null;
    private ParentRepository $parentRepository;
    private AdoRepository $adoRepository;

    protected function setUp(): void
    {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=utf8",
                $_ENV['DB_HOST'],
                $_ENV['DB_PORT'],
                $_ENV['DB_NAME']
            );
            $this->pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $this->fail("Impossible de se connecter à la base de données de test : " . $e->getMessage());
        }

        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->pdo->exec('TRUNCATE TABLE parents');
        $this->pdo->exec('TRUNCATE TABLE ados');
        $this->pdo->exec('TRUNCATE TABLE comptes');
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        // Instancier les repositories requis par le contrôleur
        $this->parentRepository = new ParentRepository($this->pdo);
        $this->adoRepository = new AdoRepository($this->pdo);

        // Supprimer les headers déjà envoyés par PHPUnit si on exécute en CLI
        if (php_sapi_name() === 'cli') {
            @header_remove();
        }
    }

    /**
     * Ferme la connexion BDD après chaque test.
     */
    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    /**
     * Test n°1 : Vérifie qu'un parent peut être créé via le formulaire.
     * On simule une requête POST et on vérifie en base de données que l'utilisateur a bien été ajouté.
     *
     * @runInSeparateProcess
     */
    public function testParentRegistrationSavesUserToDatabase(): void
    {
        // Créer un "mock" du contrôleur qui n'exécutera pas la méthode 'redirect'
        $controllerMock = $this->getMockBuilder(PageController::class)
            ->setConstructorArgs([$this->parentRepository, $this->adoRepository])
            ->onlyMethods(['redirect'])
            ->getMock();

        // On s'attend à ce que la méthode 'redirect' soit appelée une fois avec '/dashboard'
        $controllerMock->expects($this->once())
            ->method('redirect')
            ->with('/dashboard');

        // Simuler une requête POST
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['nom'] = 'John Doe';
        $_POST['email'] = 'john.doe@example.com';
        $_POST['password'] = 'password123';

        // Exécuter la méthode du contrôleur MOCKÉ
        $controllerMock->registerParent();

        // Vérifier dans la base de données (cette partie est maintenant atteignable)
        $stmt = $this->pdo->query("SELECT * FROM parents WHERE email = 'john.doe@example.com'");
        $parent = $stmt->fetch();

        $this->assertIsArray($parent, "Le parent aurait dû être trouvé en base de données.");
        $this->assertEquals('John Doe', $parent['nom']);
        $this->assertTrue(password_verify('password123', $parent['password']));
    }

    /**
     * Test n°2 : Vérifie le flux complet : création d'un ado, puis vérification de son affichage sur le dashboard.
     *
     * @runInSeparateProcess
     */
    public function testAdoRegistrationAndDisplayOnDashboard(): void
    {
        // Créer le mock pour la première étape
        $controllerMock = $this->getMockBuilder(PageController::class)
            ->setConstructorArgs([$this->parentRepository, $this->adoRepository])
            ->onlyMethods(['redirect'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('redirect')
            ->with('/dashboard');

        // --- Étape 1 : Inscription de l'ado ---
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['nom'] = 'Jane Doe';
        $_POST['hebdo'] = '15';

        $controllerMock->registerAdo();

        // Vérifier en base de données
        $stmtAdo = $this->pdo->query("SELECT * FROM ados WHERE nom = 'Jane Doe'");
        $ado = $stmtAdo->fetch();
        $this->assertEquals('15', $ado['argent_hebdo']);

        // --- Étape 2 : Affichage sur le dashboard ---

        // On a besoin d'une vraie instance du contrôleur pour cette partie
        $realController = new PageController($this->parentRepository, $this->adoRepository);

        // On capture la sortie HTML pour la vérifier
        ob_start();
        $realController->dashboard();
        $output = ob_get_clean();

        $this->assertStringContainsString('Jane Doe', $output, "Le nom de l'ado devrait apparaître sur le dashboard.");
        $this->assertStringContainsString('15 €', $output, "L'argent hebdo devrait apparaître sur le dashboard.");
        $this->assertStringContainsString('Créer un compte', $output, "Le lien pour créer un compte devrait être visible.");
    }
}
