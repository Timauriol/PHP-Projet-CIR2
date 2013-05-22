<?php
include("db.php");
include("utilisateur.class.php");

$db->exec("TRUNCATE TABLE conges;");

$utilisateurs = Utilisateur::tous();

function ferie(){return false;}

$annee = $_GET["annee"];
foreach($utilisateurs as $u){
    $query = "INSERT INTO conges (login, date, type) VALUES ";
    $date = new DateTime($annee . "-01-01", new DateTimeZone("Europe/Paris"));
    while($date->format("Y") == $annee){
        if($date->format("w") == 0 || $date->format("w") == 6)
            $query .= "(:login, '" . $date->format("Y-m-d H:i:s") . "', 'weekend'),";
        else if(ferie($date))
            $query .= "(:login, '" . $date->format("Y-m-d H:i:s") . "', 'ferie'),";
        $date->add(new DateInterval("PT12H"));
    }
    $query = substr($query, 0, -1); // on enlève la virgule en trop
    $req = $db->prepare($query);
    $req->bindValue(":login", $u->login);
    if(!$req->execute()) die("oh no");
}


?>
