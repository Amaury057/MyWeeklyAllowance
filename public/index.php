<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\PageController;
use App\Repository\AdoRepository;
use App\Repository\ParentRepository;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
  $dsn = sprintf(
    "mysql:host=%s;port=%d;dbname=%s;charset=utf8",
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    $_ENV['DB_NAME']
  );
  $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur de connexion BDD : " . $e->getMessage());
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
  $r->addRoute('GET', '/', 'home');
  $r->addRoute(['GET', 'POST'], '/register-parent', 'registerParent');
  $r->addRoute(['GET', 'POST'], '/register-ado', 'registerAdo');
  $r->addRoute('GET', '/dashboard', 'dashboard');
  $r->addRoute('GET', '/create-compte-for-ado', 'createCompteForAdo');
  $r->addRoute('POST', '/depot', 'depotManuel');
  $r->addRoute('POST', '/update-hebdo', 'updateHebdo');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
  $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// Créer les dépendances (repositories)
$parentRepository = new ParentRepository($pdo);
$adoRepository = new AdoRepository($pdo);
// Injecter les dépendances dans le contrôleur
$controller = new PageController($parentRepository, $adoRepository);

switch ($routeInfo[0]) {
  case FastRoute\Dispatcher::NOT_FOUND:
    http_response_code(404);
    echo '404 - Page non trouvée';
    break;
  case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
    http_response_code(405);
    echo '405 - Méthode non autorisée';
    break;
  case FastRoute\Dispatcher::FOUND:
    $handler = $routeInfo[1];
    if (method_exists($controller, $handler)) {
      $controller->$handler();
    } else {
      http_response_code(500);
      echo "Erreur : le handler '$handler' n'existe pas dans le contrôleur.";
    }
    break;
}
