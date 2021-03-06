<?php

function redirect($loc = "."){
    // redirige l'utilisateur
    // ⚠ doit être utilisée au tout début de la page, avant tout affichage
    header("HTTP/1.1 302 Found");
    header("Location: " . $loc);
}

function ferie($date){
    // renvoie si une date (au format DateTime()) correspond à un jour ferié ou non
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
    // ↓ timestamp de la date de pâques
    $tspaques = easter_date($date->format("Y"));
    $jferie = DateTime::createFromFormat("U", $tspaques);
    $di = new DateInterval("P1D"); // intervalle de 1 jour
    $jferie->add($di); // lundi de pâques
    if($jour == $jferie->format("j") && $mois == $jferie->format("n")) return true;
    $di = new DateInterval("P38D"); // intervalle de 38 jours
    $jferie->add($di); // jeudi de l'ascension
    if($jour == $jferie->format("j") && $mois == $jferie->format("n")) return true;
    $di = new DateInterval("P11D"); // intervalle de 11 jours
    $jferie->add($di); // lundi de la pentecôte
    if($jour == $jferie->format("j") && $mois == $jferie->format("n")) return true;

    return false;

}

include_once("db.class.php");

function estConnecte(){
    // renvoie si l'utilisateur à une session valide
    session_start();
    if(!isset($_SESSION["conge_login"])) return false;
    $db = DB::getInstance()->getPdo();
    $req = $db->prepare("SELECT 1 FROM utilisateur WHERE login = ?");
    $req->execute(array($_SESSION["conge_login"]));
    $res = $req->fetch();
    if (!$res) return false;
    else return true;
}

?>
