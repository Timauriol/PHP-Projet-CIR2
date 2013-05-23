<?php
include("db.php");
include("utilisateur.class.php");

$db->exec("TRUNCATE TABLE conges;");

$utilisateurs = Utilisateur::tous();

function ferie($date){
    $jour = $date->format("j");
    $mois = $date->format("n");
    if(
        ($jour == 1  && $mois == 1 ) || // premier janvier : jour de l'an
        ($jour == 1  && $mois == 5 ) || // premier mai : fête du travail
        ($jour == 8  && $mois == 5 ) || // 8 mai : fête de la victoire
        ($jour == 14 && $mois == 7 ) || // 14 juillet
        ($jour == 15 && $mois == 8 ) || // 15 août : assomption
        ($jour == 1  && $mois == 11) || // premier novembre : toussaint
        ($jour == 11 && $mois == 11) || // 11 novembre : armistice
        ($jour == 25 && $mois == 12)    // 25 décembre : noël
    ) return true;
    $tspaques = easter_date($date->format("Y"));
    $jferie = DateTime::createFromFormat("U", $tspaques);
    $jferie->add(new DateInterval("P1D")); // lundi de pâques
    if($jour == $jferie->format("j") && $mois == $jferie->format("n")) return true;
    $jferie->add(new DateInterval("P38D")); // jeudi de l'ascension
    if($jour == $jferie->format("j") && $mois == $jferie->format("n")) return true;
    $jferie->add(new DateInterval("P11D")); // lundi de la pentecôte
    if($jour == $jferie->format("j") && $mois == $jferie->format("n")) return true;

    return false;

}

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
