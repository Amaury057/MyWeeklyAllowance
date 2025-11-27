<!DOCTYPE html>
<html>

<head>
    <title>Tableau de bord</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body style="font-family:sans-serif; padding:20px;">
    <h1>Tableau de bord de la Famille</h1>
    <p><a href="/">Retour accueil</a> | <a href="/register-ado">Ajouter un Ado</a></p>

    <h3>Liste des Ados</h3>
    <?php if (empty($ados)): ?>
        <p>Aucun ado enregistré pour le moment.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Nom</th>
                <th>Argent Hebdo</th>
                <th>Compte</th>
            </tr>
            <?php foreach ($ados as $ado): ?>
                <tr>
                    <td><?= htmlspecialchars($ado['nom']) ?></td>
                    <td>
                        <?php if ($ado['compte_id']): ?>
                            <strong><?= htmlspecialchars($ado['argent_hebdo'] ?? 0) ?> €</strong>

                            <form method="POST" action="/update-hebdo" style="display:inline-flex; align-items:center; margin-left:15px; gap:5px;">
                                <input type="hidden" name="ado_id" value="<?= $ado['id'] ?>">
                                <input type="number" name="new_hebdo" min="0" value="<?= htmlspecialchars($ado['argent_hebdo'] ?? 0) ?>" style="width:70px;" required>
                                <button type="submit">Mettre à jour</button>
                            </form>
                        <?php else: ?>
                            <span><?= htmlspecialchars($ado['argent_hebdo'] ?? 0) ?> € <em style="color:grey;">(Compte requis pour modifier)</em></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($ado['compte_id']): ?>
                            <strong style="color:green;">Solde : <?= htmlspecialchars($ado['solde']) ?> €</strong>

                            <form method="POST" action="/depot" style="display:inline-flex; align-items:center; margin-left:15px; gap:5px;">
                                <input type="hidden" name="compte_id" value="<?= $ado['compte_id'] ?>">
                                <input type="number" name="montant" min="1" placeholder="Montant" style="width:70px;" required>
                                <button type="submit">Déposer</button>
                            </form>
                        <?php else: ?>
                            <a href="/create-compte-for-ado?ado_id=<?= $ado['id'] ?>">Créer un compte</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>

</html>