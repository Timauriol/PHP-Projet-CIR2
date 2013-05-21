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
            $req->bindParam(':login', $login);
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

    public static function tous(){
        // renvoie un Array de tous les utilisateurs
        global $db;
        $req = $db->prepare("SELECT login, nom_prenom, admin FROM utilisateur");
        if(!$req->execute()) die("Problème de connexion à la base MySQL");
        $tous = array();
        while($res = $req->fetch()){
            array_push($tous, new Utilisateur($res[0], $res[1], $res[2]));
        }
        return $tous;
    }
    public static function recherche($q){
        // renvoie un Array des utilisateurs correspondant à la recherche
        global $db;
        $query = "SELECT login, nom_prenom, admin FROM utilisateur WHERE 1=1";
        $args = explode(" ", $q);

        for($i=0; $i < count($args); $i++)
            $query .= " AND ( login COLLATE utf8_general_ci LIKE ? OR nom_prenom COLLATE utf8_general_ci LIKE ? )";

        $req = $db->prepare($query);

        for($i=0; $i < count($args); $i++){
            $arg = "%".$args[$i]."%";
            $req->bindParam($i*2+1, $arg);
            $req->bindParam($i*2+2, $arg);
        }


        $res = $req->execute();


        $utilisateurs = array();
        while($res = $req->fetch()){
            array_push($utilisateurs, new Utilisateur($res[0], $res[1], $res[2]));
        }
        return $utilisateurs;
    }
}
?>
