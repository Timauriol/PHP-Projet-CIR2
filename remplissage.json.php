<?php
/*
recoit une année et une liste de congés, et initialise cette année avec ceux-ci
*/
include_once("db.class.php");
include_once("outils.php");
include_once("utilisateur.class.php");
include_once("conge.class.php");

header("Content-Type: application/json");

if(!estConnecte()){
    // on envoie un 403 : Accès refusé
    header("HTTP/1.1 403 Forbidden");
    echo("[]");
}
else if($_SERVER['REQUEST_METHOD'] != "POST"){
    // cette page est seulement accessible en POST
    // on renvoie donc un 405 Méthode refusée en cas de GET (ou autre)
    header("HTTP/1.1 405 Method Not Allowed");
}
else {
    $utilisateurs = Utilisateur::tous();

    $post = json_decode(file_get_contents("php://input"), true);
    /*
    php://input : données reçues en POST, ici, un objet du type:
        {
            "annee": 1234,
            "solde": 4,      -- demi-journées à affecter à chaque employé pour cette année
            "conges": [
                {"date": "2000-12-31 00:00:00"},
                ...
            ]
        }
    */

    $annee = $post["annee"];

    $db = DB::getInstance()->getPdo();
    // on remet à zéro les congés de l'année
    $req = $db->prepare("DELETE FROM conges WHERE date >= :date AND date <= (:date + INTERVAL 11 MONTH + INTERVAL 30 DAY + INTERVAL 12 HOUR)");
    $req->bindValue(":date", $annee . "-01-01");
    $req->execute();

    // on met les soldes à la valeur reçue
    Utilisateur::setSoldeTous($post["solde"], $annee);

    foreach($utilisateurs as $u){
        $query = "INSERT INTO conges (login, date, type) VALUES ";
        $tz = new DateTimeZone("Europe/Paris");
        $date = new DateTime($annee . "-01-01", $tz);
        $douzeheures = new DateInterval("PT12H");

        // on ajoute d'abord les feriés et week-ends
        while($date->format("Y") == $annee){
            if(ferie($date))
                $query .= "(:login, '" . $date->format("Y-m-d H:i:s") . "', 'ferie'),";
            else if($date->format("w") == 0 || $date->format("w") == 6) // week-end
                $query .= "(:login, '" . $date->format("Y-m-d H:i:s") . "', 'weekend'),";
            $date->add($douzeheures);
        }
        $query = substr($query, 0, -1); // on enlève la virgule en trop
        $req = $db->prepare($query);
        $req->bindValue(":login", $u->login);
        $req->execute();

        // puis les congés envoyés
        $conges = array();
        foreach($post['conges'] as $conge){
            $c = new Conge($conge['date'], $u->login);
            array_push($conges, $c);
        }
        Conge::insererListe($conges);
    }
}
?>
