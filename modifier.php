<?php
require_once 'Database.php';
require_once 'Livre.php';

$db = (new Database())->getConnection();
$livreObj = new Livre($db);

// 1. On récupère l'ID du livre à modifier
$livreObj->id = isset($_GET['id']) ? $_GET['id'] : die('Erreur : ID manquant.');

// 2. On charge les données actuelles du livre
$livreObj->readOne();

// 3. Récupération des auteurs et catégories pour les menus
$auteurs = $db->query("SELECT id, nom, prenom FROM auteurs")->fetchAll(PDO::FETCH_ASSOC);
$categories = $db->query("SELECT id, libelle FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// 4. Traitement de la modification après clic sur le bouton
if ($_POST) {
    $livreObj->titre = $_POST['titre'];
    $livreObj->isbn = $_POST['isbn'];
    $livreObj->annee = $_POST['annee'];
    $livreObj->quantite = $_POST['quantite'];
    $livreObj->auteur_id = $_POST['auteur_id'];
    $livreObj->categorie_id = $_POST['categorie_id'];

    if($livreObj->update()) {
        header("Location: index.php?msg=updated");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Livre</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .form-container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; }
        button { background: #ffc107; color: black; border: none; padding: 10px; width: 100%; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>✏️ Modifier le livre</h2>
        <form method="POST">
            <input type="text" name="titre" value="<?= htmlspecialchars($livreObj->titre) ?>" required>
            <input type="text" name="isbn" value="<?= htmlspecialchars($livreObj->isbn) ?>" required>
            <input type="number" name="annee" value="<?= $livreObj->annee ?>" required>
            <input type="number" name="quantite" value="<?= $livreObj->quantite ?>" required>
            
            <label>Auteur :</label>
            <select name="auteur_id">
                <?php foreach($auteurs as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($a['id'] == $livreObj->auteur_id) ? 'selected' : '' ?>>
                        <?= $a['prenom'] . " " . $a['nom'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Catégorie :</label>
            <select name="categorie_id">
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($c['id'] == $livreObj->categorie_id) ? 'selected' : '' ?>>
                        <?= $c['libelle'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enregistrer les modifications</button>
        </form>
        <br><a href="index.php">Annuler</a>
    </div>
</body>
</html>