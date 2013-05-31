<?php
include_once("db.php");
include_once("outils.php");
include_once("utilisateur.class.php");

session_start();

if(!isset($_SESSION["conge_login"]) || $_SESSION["conge_login"] === ""){
    $u = new Utilisateur($_SESSION["conge_login"]);
    if($u->login === ""){
        header("HTTP/1.1 403 Forbidden");
        die("[]");
    }
}
if(!isset($_SESSION["conge_login"]) || $_SESSION["conge_login"] === "" || (new Utilisateur($_SESSION["conge_login"]))->login === ""){
    header("HTTP/1.1 403 Forbidden");
    die();
}


include_once("conge.class.php");

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] != "POST"){
    header("HTTP/1.1 405 Method Not Allowed");
    die();
}

$utilisateurs = Utilisateur::tous();

$post = json_decode(file_get_contents("php://input"), true);

$annee = $post["annee"];

$req = $db->prepare("DELETE FROM conges WHERE date >= :date AND date <= (:date + INTERVAL 11 MONTH + INTERVAL 30 DAY + INTERVAL 12 HOUR)");
$req->bindValue(":date", $annee . "-01-01");
$req->execute();

Utilisateur::setSoldeTous($post["solde"], $annee);

foreach($utilisateurs as $u){
    $query = "INSERT INTO conges (login, date, type) VALUES ";
    $tz = new DateTimeZone("Europe/Paris");
    $date = new DateTime($annee . "-01-01", $tz);
    while($date->format("Y") == $annee){
        if(ferie($date))
            $query .= "(:login, '" . $date->format("Y-m-d H:i:s") . "', 'ferie'),";
        else if($date->format("w") == 0 || $date->format("w") == 6)
            $query .= "(:login, '" . $date->format("Y-m-d H:i:s") . "', 'weekend'),";
        $date->add(new DateInterval("PT12H"));
    }
    $query = substr($query, 0, -1); // on enlève la virgule en trop
    $req = $db->prepare($query);
    $req->bindValue(":login", $u->login);
    $req->execute();

    $conges = array();
    foreach($post['conges'] as $conge){
        array_push($conges, new Conge($conge['date'], $u->login));
    }
    Conge::insererListe($conges);
}

?>
