<?php
require_once 'Database.php';
require_once 'Emprunt.php';
require_once 'Livre.php';

$db = (new Database())->getConnection();
$empruntObj = new Emprunt($db);
$livreObj = new Livre($db);

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($empruntObj->create($_POST['livre_id'], $_POST['nom_emprunteur'], $_POST['date_retour'])) {
        echo "<script>alert('Emprunt enregistré !'); window.location='gestion_emprunts.php';</script>";
    } else {
        echo "<script>alert('Erreur : Stock épuisé ou problème technique.');</script>";
    }
}

$livres = $livreObj->read();
$emprunts = $empruntObj->readAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Emprunts</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #17a2b8; padding-bottom: 10px; }
        form { background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 30px; }
        select, input { padding: 10px; margin: 10px 0; width: 100%; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #17a2b8; color: white; }
        .btn-action { text-decoration: none; font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 12px; }
        .edit { color: #856404; background: #fff3cd; border: 1px solid #ffeeba; }
        .delete { color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; }
        .back { display: inline-block; margin-bottom: 15px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back">← Retour à l'accueil</a>
        <h2>🤝 Nouvel Emprunt</h2>
        <form method="POST">
            <label>Livre :</label>
            <select name="livre_id" required>
                <?php while ($l = $livres->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titre']) ?> (Stock: <?= $l['quantite'] ?>)</option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="nom_emprunteur" placeholder="Nom complet de l'emprunteur" required>
            <label>Date de retour prévue :</label>
            <input type="date" name="date_retour" required>
            <button type="submit" style="background: #17a2b8; color: white; border: none; padding: 12px; width: 100%; cursor: pointer; border-radius: 5px; font-weight: bold;">Enregistrer l'emprunt</button>
        </form>

        <h2>📋 Liste des Emprunteurs en cours</h2>
        <table>
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Emprunteur</th>
                    <th>Date Retour Prévue</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $emprunts->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['titre']) ?></td>
                    <td><?= htmlspecialchars($row['nom_emprunteur']) ?></td>
                    <td><?= $row['date_retour_prevue'] ?></td>
                    <td>
                        <a href="modifier_emprunt.php?id=<?= $row['id'] ?>" class="btn-action edit">Modifier</a>
                        <a href="supprimer_emprunt.php?id=<?= $row['id'] ?>" class="btn-action delete" onclick="return confirm('Supprimer cet emprunt ? Le stock sera mis à jour.')">Supprimer</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>