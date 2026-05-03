<?php
require_once 'Database.php';
require_once 'Livre.php';

// Initialisation de la connexion
$database = new Database();
$db = $database->getConnection();

// Initialisation de l'objet Livre
$livreObj = new Livre($db);
$livres = $livreObj->read();

// Message de confirmation (optionnel)
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Bibliothèque - Gestion</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 30px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; margin-bottom: 30px; }
        
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
        th, td { padding: 15px; border: 1px solid #eaeaea; text-align: left; }
        th { background-color: #007bff; color: white; text-transform: uppercase; font-size: 14px; }
        tr:hover { background-color: #f9f9f9; }

        /* Styles des boutons */
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 13px; font-weight: bold; color: white; display: inline-block; transition: 0.3s; }
        .btn-add { background-color: #28a745; margin-bottom: 10px; }
        .btn-add:hover { background-color: #218838; }
        .btn-edit { background-color: #ffc107; color: #212529; margin-right: 5px; }
        .btn-edit:hover { background-color: #e0a800; }
        .btn-delete { background-color: #dc3545; }
        .btn-delete:hover { background-color: #c82333; }
        .btn-borrow { background-color: #17a2b8; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="container">
    <h1>📚 Système de Gestion de Bibliothèque</h1>

    <?php if($msg == 'success'): ?>
        <div class="alert alert-success">✅ Livre ajouté avec succès !</div>
    <?php elseif($msg == 'updated'): ?>
        <div class="alert alert-success">✏️ Livre mis à jour avec succès !</div>
    <?php endif; ?>

    <div class="header-actions">
        <a href="ajouter.php" class="btn btn-add">➕ Ajouter un Livre</a>
        <a href="gestion_emprunts.php" class="btn btn-borrow">🤝 Gérer les Emprunts</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>ISBN</th>
                <th>Auteur</th>
                <th>Catégorie</th>
                <th>Stock</th>
                <th style="width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $livres->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><strong><?= htmlspecialchars($row['titre']) ?></strong></td>
                <td><?= htmlspecialchars($row['isbn']) ?></td>
                <td><?= htmlspecialchars($row['auteur_nom']) ?></td>
                <td><?= htmlspecialchars($row['cat_nom']) ?></td>
                <td>
                    <span style="color: <?= ($row['quantite'] > 0) ? 'green' : 'red' ?>;">
                        <?= $row['quantite'] ?> disponible(s)
                    </span>
                </td>
                <td>
                    <!-- BOUTON MODIFIER -->
                    <a href="modifier.php?id=<?= $row['id'] ?>" class="btn btn-edit">Modifier</a>
                    
                    <!-- BOUTON SUPPRIMER -->
                    <a href="supprimer.php?id=<?= $row['id'] ?>" 
                       class="btn btn-delete" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?');">
                       Supprimer
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>