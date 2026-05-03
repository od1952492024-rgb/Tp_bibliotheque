<?php
require_once 'Database.php';
require_once 'Livre.php';

$database = new Database();
$db = $database->getConnection();
$livreObj = new Livre($db);

// Récupération des auteurs et catégories pour les <select>
try {
    $auteurs = $db->query("SELECT id, nom, prenom FROM auteurs ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
    $categories = $db->query("SELECT id, libelle FROM categories ORDER BY libelle ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de récupération des données : " . $e->getMessage());
}

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $livreObj->titre = $_POST['titre'];
    $livreObj->isbn = $_POST['isbn'];
    $livreObj->annee = $_POST['annee'];
    $livreObj->quantite = $_POST['quantite'];
    $livreObj->auteur_id = $_POST['auteur_id'];
    $livreObj->categorie_id = $_POST['categorie_id'];

    if($livreObj->create()) {
        // Redirection vers l'accueil après succès
        header("Location: index.php?msg=success");
        exit();
    } else {
        $error = "Erreur lors de l'ajout du livre.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Livre - Gestion Bibliothèque</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; padding: 40px; }
        .container { max-width: 600px; margin: auto; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
        input, select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { background-color: #28a745; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 25px; transition: background 0.3s; }
        .btn-submit:hover { background-color: #218838; }
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #007bff; }
        .error { color: #dc3545; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

    <div class="container">
        <h2>➕ Ajouter un Livre</h2>

        <?php if(isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Titre du livre</label>
            <input type="text" name="titre" placeholder="Ex: L'Étranger" required>

            <label>Code ISBN</label>
            <input type="text" name="isbn" placeholder="Ex: 978-2070360024" required>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Année</label>
                    <input type="number" name="annee" placeholder="2024" required>
                </div>
                <div style="flex: 1;">
                    <label>Quantité Stock</label>
                    <input type="number" name="quantite" placeholder="10" min="1" required>
                </div>
            </div>

            <label>Auteur</label>
            <select name="auteur_id" required>
                <option value="">-- Sélectionner un auteur --</option>
                <?php foreach($auteurs as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['prenom'] . " " . $a['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Catégorie</label>
            <select name="categorie_id" required>
                <option value="">-- Sélectionner une catégorie --</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['libelle']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-submit">Enregistrer dans la bibliothèque</button>
        </form>

        <a href="index.php" class="back-link">← Retour à la liste des livres</a>
    </div>

</body>
</html>