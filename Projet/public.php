<?php
spl_autoload_register(function ($className) {
    include ("lib/{$className}.class.php");
});
require_once("lib/session_start.php");
if (isset($_SESSION['ident'])){
    $personne = $_SESSION['ident'];
}

date_default_timezone_set ('Europe/Paris');
try{
    $data = new DataLayer();
/**   $listeEquipes = $data->getEquipes();
*    $listeEtapes = $data->getEtapes();
*    $coureurs = $data->getCoureurs();
*    $stats = $data->getStats();
**/  require ('views/Page.php');
} catch (PDOException $e){
    $errorMessage = $e->getMessage();
    require("views/pageErreur.php");
}

?>
