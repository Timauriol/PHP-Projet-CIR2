<?php
include_once("db.php");

class Utilisateur{
    public $login = "";
    public $nom_prenom = "";
    public $admin = false;

    function __construct($login, $nom_prenom = null, $admin = null){
        global $db;
        /* Utilisateur("ppouch") vérifie le login et récupère les infos dans la BDD
           Utilisateur("ppouch", "Pascal Pouchard", false) remplit seulement les champs 
           utile si l'on récupère plusieurs utilisateurs dans une même requète */
        if(!$nom_prenom || !$admin){
            $req = $db->prepare("SELECT nom_prenom, admin FROM utilisateur WHERE login = :login");
            $req->bindValue(':login', $login);
            if(!$req->execute()) die("Problème de connexion à la base MySQL");
            $res = $req->fetch();
            if(!$res) // l'utilisateur n'existe pas
                return;
            $this->login = $login;
            $this->nom_prenom = $res["nom_prenom"];
            $this->admin = $res["admin"] == "1"; // beurk
        }
        else{
            $this->login = $login;
            $this->nom_prenom = $nom_prenom;
            $this->admin = $admin == "1";
        }
    }


    public function getSolde($annee){
        global $db;
        $req = $db->prepare("SELECT solde FROM solde WHERE login = ? AND annee = ?");
        $req->execute(array($this->login, $annee));
        $res = $req->fetch();
        if(!$res){
            $req = $db->prepare("INSERT INTO solde (solde, login, annee) values (0,?,?)");
            $req->execute(array($this->login, $annee));
            return 0;
        }
        else{
            return $res[0];
        }
    }

    public static function staticEditSolde($login, $modif, $annee){ // relatif, ie. $u->setSolde(-1, 2013) pour réduire le solde de 1
        global $db;
        $req = $db->prepare("UPDATE solde SET solde = solde + ? WHERE login = ? AND annee = ?");
        $req->bindValue(1, $modif);
        $req->bindValue(2, $login);
        $req->bindValue(3, $annee, PDO::PARAM_INT);
        $req->execute();
        if($req->rowCount() == 0){
            $req = $db->prepare("INSERT INTO solde (solde, login, annee) values (?,?,?)");
            $req->bindValue(1, $modif);
            $req->bindValue(2, $login);
            $req->bindValue(3, $annee, PDO::PARAM_INT);
            $req->execute();
        }
    }

    public function editSolde($modif, $annee){
        Utilisateur::staticEditSolde($this->login, $modif, $annee);
    }

    public static function tous(){
        // renvoie un Array de tous les utilisateurs
        global $db;
        $req = $db->prepare("SELECT login, nom_prenom, admin FROM utilisateur");
        if(!$req->execute()) die("Problème de connexion à la base MySQL");
        $tous = array();
        while($res = $req->fetch())
            array_push($tous, new Utilisateur($res[0], $res[1], $res[2]));
        return $tous;
    }

    public static function setSoldeTous($solde, $annee){
        global $db;
        $req = $db->prepare("DELETE FROM solde WHERE annee = ?");
        $req->execute(array($annee));
        $query = "INSERT INTO solde (solde, login, annee) VALUES ";
        $tous = Utilisateur::tous();
        $vars = array();
        foreach($tous as $u){
            $query .= "(?, ?, ?),";
            array_push($vars, $solde, $u->login, $annee);
        }
        $query = substr($query, 0, -1);
        $req = $db->prepare($query);
        if(!$req->execute($vars)){
            var_dump($req->errorInfo());
            die();
        }
    }

    public static function recherche($q, $limite = 0){
        // renvoie un Array des utilisateurs correspondant à la recherche
        global $db;

        if($q === "") return array();

        $query = "SELECT login, nom_prenom, admin FROM utilisateur WHERE 1=1";

        $args = explode(" ", trim($q));
        for($i=0; $i < count($args); $i++)
            $query .= " AND ( login COLLATE utf8_general_ci LIKE ? OR nom_prenom COLLATE utf8_general_ci LIKE ? )";
            // les COLLATE blabla sont pour rendre la recherche insensible à la casse

        if($limite != 0) $query .= " LIMIT " . $limite;
        $req = $db->prepare($query);

        for($i=0; $i < count($args); $i++){
            $arg = "%".$args[$i]."%"; // % : wildcard
            $req->bindValue($i*2+1, $arg); // les paramètres commencent à 1
            $req->bindValue($i*2+2, $arg);
        }

        $res = $req->execute();

        $utilisateurs = array();
        while($res = $req->fetch())
            array_push($utilisateurs, new Utilisateur($res[0], $res[1], $res[2]));
        return $utilisateurs;
    }
}
?>
