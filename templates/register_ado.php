<!DOCTYPE html>
<html>
<head><title>Inscription Ado</title></head>
<body style="font-family:sans-serif; padding:20px;">
    <a href="/">Retour accueil</a>
    <h2>Inscription Ado</h2>
    <?php if (!empty($message)) echo "<p style='color:red'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="nom" placeholder="Prénom de l'Ado" required><br><br>
        <!-- <label>Argent de poche hebdo (€) :</label>
        <input type="number" name="hebdo" value="10"><br><br> -->
        <button type="submit">Créer le compte Ado</button>
    </form>
</body>
</html>