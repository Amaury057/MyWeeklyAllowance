<!DOCTYPE html>
<html>
<head><title>Inscription Parent</title></head>
<body style="font-family:sans-serif; padding:20px;">
    <a href="/">Retour accueil</a>
    <h2>Inscription Parent</h2>
    <?php if (!empty($message)) echo "<p style='color:red'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="nom" placeholder="Nom" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br><br>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>