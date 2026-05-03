<?php
require_once 'Database.php';
require_once 'Livre.php';

if(isset($_GET['id'])) {
    $db = (new Database())->getConnection();
    $livre = new Livre($db);
    $livre->id = $_GET['id'];
    if($livre->delete()) {
        header("Location: index.php");
    }
}
?>