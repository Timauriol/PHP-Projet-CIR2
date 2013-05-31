<?php
include_once("db.php");
include_once("utilisateur.class.php");
include_once("outils.php");

header("Content-Type: application/json");

if(!estConnecte()){
    header("HTTP/1.1 403 Forbidden");
    echo("[]")
}
else {


    include_once("conge.class.php");

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $conges = json_decode(file_get_contents("php://input"), true);
        foreach($conges as $conge){
            $c = new Conge($conge['date'], $conge['login']);
            $d = new DateTime($c->date);
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
            $d = new DateTime($c->date);
        ?>
            {
                "date": "<?=$c->date?>",
                "ts": <?=$d->getTimestamp()?>,
                "login": "<?=strtr($c->login, $echappement)?>"
            }
        <?php
        }
        echo("]");
    }
}
?>
