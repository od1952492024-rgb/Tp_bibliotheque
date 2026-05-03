<?php
require_once 'Database.php';
require_once 'Emprunt.php';

$db = (new Database())->getConnection();
$empruntObj = new Emprunt($db);

$empruntObj->id = isset($_GET['id']) ? $_GET['id'] : die('ID manquant');
$empruntObj->readOne();

if ($_POST) {
    $empruntObj->nom_emprunteur = $_POST['nom_emprunteur'];
    $empruntObj->date_retour_prevue = $_POST['date_retour'];

    if($empruntObj->update()) {
        header("Location: gestion_emprunts.php");
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Emprunt</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; box-sizing: border-box; }
        button { background: #17a2b8; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ Modifier l'emprunteur</h2>
        <form method="POST">
            <label>Nom de l'emprunteur :</label>
            <input type="text" name="nom_emprunteur" value="<?= htmlspecialchars($empruntObj->nom_emprunteur) ?>" required>
            
            <label>Nouvelle date de retour :</label>
            <input type="date" name="date_retour" value="<?= $empruntObj->date_retour_prevue ?>" required>
            
            <button type="submit">Enregistrer les modifications</button>
        </form>
        <br><a href="gestion_emprunts.php">Annuler</a>
    </div>
</body>
</html>