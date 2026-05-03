<?php
class Livre {
    private $conn;
    private $table = "livres";

    public $id;
    public $titre;
    public $isbn;
    public $annee;
    public $quantite;
    public $auteur_id;
    public $categorie_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lire tous les livres avec jointures pour les noms d'auteurs et catégories
    public function read() {
        $query = "SELECT l.*, a.nom as auteur_nom, c.libelle as cat_nom 
                  FROM " . $this->table . " l
                  LEFT JOIN auteurs a ON l.auteur_id = a.id
                  LEFT JOIN categories c ON l.categorie_id = c.id
                  ORDER BY l.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Récupérer les données d'un seul livre par son ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->titre = $row['titre'];
            $this->isbn = $row['isbn'];
            $this->annee = $row['annee'];
            $this->quantite = $row['quantite'];
            $this->auteur_id = $row['auteur_id'];
            $this->categorie_id = $row['categorie_id'];
        }
    }

    // Créer un nouveau livre
    public function create() {
        $query = "INSERT INTO " . $this->table . " SET titre=:titre, isbn=:isbn, annee=:annee, quantite=:quantite, auteur_id=:auteur_id, categorie_id=:categorie_id";
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt);
        return $stmt->execute();
    }

    // Mettre à jour un livre existant
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET titre=:titre, isbn=:isbn, annee=:annee, quantite=:quantite, 
                      auteur_id=:auteur_id, categorie_id=:categorie_id 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $this->bindParams($stmt);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Supprimer un livre
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    // Fonction interne pour lier les paramètres (évite la répétition)
    private function bindParams($stmt) {
        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":isbn", $this->isbn);
        $stmt->bindParam(":annee", $this->annee);
        $stmt->bindParam(":quantite", $this->quantite);
        $stmt->bindParam(":auteur_id", $this->auteur_id);
        $stmt->bindParam(":categorie_id", $this->categorie_id);
    }
}
?>