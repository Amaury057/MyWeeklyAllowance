<?php

namespace App\Controller;

use App\Entity\Ado;
use App\Entity\Compte;
use App\Entity\ParentUser;
use App\Repository\AdoRepository;
use App\Repository\ParentRepository;
use PDO;

class PageController
{
    public function __construct(
        private ParentRepository $parentRepository,
        private AdoRepository $adoRepository
    ) {}

    public function home(): void
    {
        require_once __DIR__ . '/../../templates/home.php';
    }

    public function registerParent(): void
    {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nom = htmlspecialchars($_POST['nom'] ?? '');
                $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
                $password = $_POST['password'] ?? '';

                if (empty($nom) || empty($email) || empty($password)) {
                    throw new \Exception("Tous les champs sont requis.");
                }

                $parent = new ParentUser();
                $parent->setNom($nom)
                    ->setEmail($email)
                    ->setPassword($password);

                if ($this->parentRepository->save($parent)) {
                    $this->redirect('/dashboard');
                } else {
                    throw new \Exception("Erreur lors de la création du compte parent.");
                }
            } catch (\PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "Cet email est déjà utilisé.";
                } else {
                    $message = "Erreur Base de données: " . $e->getMessage();
                }
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
        }

        require_once __DIR__ . '/../../templates/register_parent.php';
    }

    public function registerAdo(): void
    {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nom = htmlspecialchars($_POST['nom'] ?? '');
                $hebdo = filter_var($_POST['hebdo'] ?? 0, FILTER_VALIDATE_INT);

                if (empty($nom) || $hebdo === false || $hebdo < 0) {
                    throw new \Exception("Les données du formulaire sont invalides.");
                }

                $ado = new Ado();
                $ado->setNom($nom)
                    ->setArgentHebdo($hebdo); // Ajout de l'argent hebdo à la création

                if ($this->adoRepository->save($ado)) {
                    $this->redirect('/dashboard');
                } else {
                    throw new \Exception("Erreur lors de la création du compte ado.");
                }
            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
        }

        require_once __DIR__ . '/../../templates/register_ado.php';
    }

    public function dashboard(): void
    {
        $ados = $this->adoRepository->findAllForDashboard();
        require_once __DIR__ . '/../../templates/dashboard.php';
    }

    public function createCompteForAdo(): void
    {
        $adoId = filter_var($_GET['ado_id'] ?? null, FILTER_VALIDATE_INT);

        if ($adoId) {
            $ado = new Ado();
            $ado->setId($adoId);

            $compte = new Compte();

            $this->adoRepository->assignCompteToAdo($compte, $ado);
        }

        $this->redirect('/dashboard');
    }

    public function depotManuel(): void
    {
        $compteId = filter_var($_POST['compte_id'] ?? null, FILTER_VALIDATE_INT);
        $montant = filter_var($_POST['montant'] ?? null, FILTER_VALIDATE_INT);

        if ($compteId && $montant && $montant > 0) {
            $this->adoRepository->addMoneyToCompte($compteId, $montant);
        }

        $this->redirect('/dashboard');
    }

    public function updateHebdo(): void
    {
        $adoId = filter_var($_POST['ado_id'] ?? null, FILTER_VALIDATE_INT);
        $newHebdo = filter_var($_POST['new_hebdo'] ?? null, FILTER_VALIDATE_INT);

        if ($adoId && $newHebdo !== false && $newHebdo >= 0) {
            if ($this->adoRepository->hasCompte($adoId)) {
                $this->adoRepository->updateArgentHebdo($adoId, $newHebdo);
            }
        }

        $this->redirect('/dashboard');
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }
}
