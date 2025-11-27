<!DOCTYPE html>
<html>
<head><title>Inscription Ado</title>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"
></head>
<body style="font-family:sans-serif; padding:20px;">
    <a href="/">Retour accueil</a>
    <h2>Inscription Ado</h2>
    <?php if (!empty($message)) echo "<p style='color:red'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="nom" placeholder="Prénom de l'Ado" required><br><br>
        <button type="submit">Créer le compte Ado</button>
    </form>
</body>
</html>