<?php
require_once 'Database.php';
require_once 'Emprunt.php';

if(isset($_GET['id'])) {
    $db = (new Database())->getConnection();
    $emprunt = new Emprunt($db);
    $emprunt->id = $_GET['id'];
    
    if($emprunt->delete()) {
        header("Location: gestion_emprunts.php");
    }
}
?>