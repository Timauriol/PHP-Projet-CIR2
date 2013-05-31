<?php
include("db.php");
include("utilisateur.class.php");


session_start();

if(!isset($_SESSION["conge_login"]) || $_SESSION["conge_login"] === "" || (new Utilisateur($_SESSION["conge_login"]))->login === ""){
    header("HTTP/1.1 403 Forbidden");
    die("[]");
}


include("conge.class.php");

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $conges = json_decode(file_get_contents("php://input"), true);
    foreach($conges as $conge){
        $c = new Conge($conge['date'], $conge['login']);
        switch($_GET["action"]){
            case "inserer":
                $c->inserer();
                Utilisateur::staticEditSolde($c->login, -1, (new DateTime($c->date))->format("Y"));
                break;
            case "supprimer":
                $c->supprimer();
                Utilisateur::staticEditSolde($c->login, +1, (new DateTime($c->date))->format("Y"));
                break;
        }
    }
}
else{ // GET (ou HEAD)

    $echappement = array('\\' => '\\\\', '"' => '\\"');

    $conges = Conge::liste(
        isset($_GET["login"])? $_GET["login"] : null,
        isset($_GET["date_debut"])? $_GET["date_debut"] : null,
        isset($_GET["date_fin"])? $_GET["date_fin"] : null,
        isset($_GET["limite"])? $_GET["limite"] : null,
        isset($_GET["type"])? $_GET["type"] : null
    );

    echo("[\n");
    $premier = true;
    foreach($conges as $c){
        if($premier)
            $premier = false;
        else
            echo(",");
    ?>
        {
            "date": "<?=$c->date?>",
            "ts": <?=(new DateTime($c->date))->getTimestamp()?>,
            "login": "<?=strtr($c->login, $echappement)?>"
        }
    <?php
    }
    echo("]");
}
?>
