<?php

include_once("outils.php");

class Conge{
    public $login;
    public $date;
    public $type;
    function __construct($date, $login, $type = null){
        $this->login = $login;
        $this->date = $date;
        if($type)
            $this->type = $type;
        else{
            $dt = new DateTime($date);
            if(ferie($dt))
                $this->type = "ferie";
            else if($dt->format("w") == 0 || $dt->format("w") == 6)
                $this->type = "weekend";
            else
                $this->type = "conge";
        }

    }
    public function supprimer(){
        global $db;
        $req = $db->prepare("DELETE FROM conges WHERE date = ? AND login = ?");
        $req->execute(array($this->date, $this->login));
    }
    public function inserer(){
        global $db;
        $req = $db->prepare("INSERT INTO conges (date, login, type) VALUES (?, ?, ?)");
        $req->execute(array($this->date, $this->login, $this->type));
    }
    public static function insererListe($conges){
        global $db;
        $query = "INSERT INTO conges (date, login, type) VALUES ";
        $args = array();
        foreach($conges as $conge){
            $query .= "(?, ?, ?),";
            array_push($args, $conge->date);
            array_push($args, $conge->login);
            array_push($args, $conge->type);
        }
        $query = substr($query, 0, -1); // on vire le "," final
        $req = $db->prepare($query);
        return($req->execute($args));
    }
    public static function liste($login = null, $date_debut = null, $date_fin = null, $limite = null, $type = null){
        // les dates sont attendues sous format ISO : 2000-12-31 (ou 2000-12-31 23:59)
        global $db;
        $query = "SELECT date, login, type FROM conges WHERE 1=1";
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
        if($type){
            $query .= " AND type = ?";
            array_push($args, $type);
        }
        $query .= " ORDER BY date ASC";
        if($limite) $query .= " LIMIT " . $limite;
        $req = $db->prepare($query);
        $res = $req->execute($args);

        $conges = array();

        while($res = $req->fetch()){
            $c = new Conge($res[0], $res[1], $res[2]);
            array_push($conges, $c);
        }

        return $conges;
    }
}

?>
