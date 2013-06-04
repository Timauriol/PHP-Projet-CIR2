<?php
/*
permet de récupérer une liste de congés en format JSON (GET)
ou d'envoyer une liste de congés en JSON à insérer ou supprimer (POST)
*/
include_once("db.class.php");
include_once("utilisateur.class.php");
include_once("outils.php");

header("Content-Type: application/json");

if(!estConnecte()){
    // on envoie un 403 : Accès refusé
    header("HTTP/1.1 403 Forbidden");
    echo("[]");
}
else {
    include_once("conge.class.php");

    // methode POST : on insère les congés envoyés dans la BDD
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        // php://input : les données recues par POST, ici une liste de congés en JSON
        $conges = json_decode(file_get_contents("php://input"), true);
        foreach($conges as $conge){
            $c = new Conge($conge['date'], $conge['login']);
            $d = new DateTime($c->date); // utilisée pour l'édition des soldes
            $annee = $d->format("Y");
            switch($_GET["action"]){
                case "inserer":
                    $c->inserer();
                    Utilisateur::staticEditSolde($c->login, -1, $annee);
                    break;
                case "supprimer":
                    $c->supprimer();
                    Utilisateur::staticEditSolde($c->login, +1, $annee);
                    break;
            }
        }
        // on ne renvoie rien
    }
    // methode GET : on envoie une liste de congés
    // correspondant à la recherche
    else{

        $conges = Conge::liste(
            isset($_GET["login"])? $_GET["login"] : null,
            isset($_GET["date_debut"])? $_GET["date_debut"] : null,
            isset($_GET["date_fin"])? $_GET["date_fin"] : null,
            isset($_GET["limite"])? $_GET["limite"] : null,
            isset($_GET["type"])? $_GET["type"] : null
        );

        echo(json_encode($conges));
    }
}
?>
