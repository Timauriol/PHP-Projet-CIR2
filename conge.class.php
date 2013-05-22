<?php

class Conge{
    public $login;
    public $date;
    function __construct($date, $login){
        $this->login = $login;
        $this->date = $date;
    }
    public static function liste($login = null, $date_debut = null, $date_fin = null, $limite = null){
        // les dates sont attendues sous format ISO : 2000-12-31 (ou 2000-12-31 23:59)
        global $db;
        $query = "SELECT date, login FROM conges WHERE 1=1";
        $args = array();
        if($login){
            $query .= " AND login = ?";
            array_push($args, $login);
        }
        if($date_debut){
            $query .= " AND date >= ?";
            array_push($args, $date_debut);
        }
        if($date_fin){
            $query .= " AND date <= ?";
            array_push($args, $date_fin);
        }
        $query .= " ORDER BY date ASC";
        if($limite) $query .= " LIMIT " . $limite;
        $req = $db->prepare($query);
        $res = $req->execute($args);

        $conges = array();

        while($res = $req->fetch())
            array_push($conges, new Conge($res[0], $res[1]));

        return $conges;
    }
}

?>
