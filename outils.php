<?php

function redirect($loc = "."){
    header("HTTP/1.1 302 Found");
    header("Location: " . $loc);
    exit();
}

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


?>
