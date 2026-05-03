<?php
class Emprunt {
    private $conn;
    private $table_name = "emprunts";

    public $id;
    public $livre_id;
    public $nom_emprunteur;
    public $date_retour_prevue;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un emprunt (avec -1 en stock)
    public function create($livre_id, $nom, $date_retour) {
        try {
            $this->conn->beginTransaction();
            $queryEmprunt = "INSERT INTO " . $this->table_name . " (livre_id, nom_emprunteur, date_emprunt, date_retour_prevue) VALUES (:livre_id, :nom, CURDATE(), :date_retour)";
            $stmt1 = $this->conn->prepare($queryEmprunt);
            $stmt1->execute([':livre_id' => $livre_id, ':nom' => $nom, ':date_retour' => $date_retour]);

            $queryStock = "UPDATE livres SET quantite = quantite - 1 WHERE id = :livre_id AND quantite > 0";
            $stmt2 = $this->conn->prepare($queryStock);
            $stmt2->execute([':livre_id' => $livre_id]);

            if ($stmt2->rowCount() == 0) throw new Exception("Stock épuisé.");
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Lire tous les emprunts
    public function readAll() {
        $query = "SELECT e.*, l.titre FROM " . $this->table_name . " e JOIN livres l ON e.livre_id = l.id ORDER BY e.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire un seul emprunt (pour la modification)
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->livre_id = $row['livre_id'];
            $this->nom_emprunteur = $row['nom_emprunteur'];
            $this->date_retour_prevue = $row['date_retour_prevue'];
        }
    }

    // Modifier un emprunteur ou une date
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nom_emprunteur = :nom, date_retour_prevue = :date WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $this->nom_emprunteur);
        $stmt->bindParam(':date', $this->date_retour_prevue);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Supprimer un emprunt (et rendre +1 au stock)
    public function delete() {
        try {
            $this->conn->beginTransaction();
            
            // On récupère l'ID du livre avant de supprimer l'emprunt pour savoir quel stock augmenter
            $queryFind = "SELECT livre_id FROM " . $this->table_name . " WHERE id = :id";
            $stmtFind = $this->conn->prepare($queryFind);
            $stmtFind->execute([':id' => $this->id]);
            $res = $stmtFind->fetch(PDO::FETCH_ASSOC);

            if($res) {
                // 1. Augmenter le stock
                $queryStock = "UPDATE livres SET quantite = quantite + 1 WHERE id = :l_id";
                $stmtUp = $this->conn->prepare($queryStock);
                $stmtUp->execute([':l_id' => $res['livre_id']]);

                // 2. Supprimer l'emprunt
                $queryDel = "DELETE FROM " . $this->table_name . " WHERE id = :id";
                $stmtDel = $this->conn->prepare($queryDel);
                $stmtDel->execute([':id' => $this->id]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>