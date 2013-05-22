<?php
header("Content-Type: application/json");

include("db.php");
include("conge.class.php");

$echappement = array('\\' => '\\\\', '"' => '\\"');

$conges = Conge::liste(
    isset($_GET["login"])? $_GET["login"] : null,
    isset($_GET["date_debut"])? $_GET["date_debut"] : null,
    isset($_GET["date_fin"])? $_GET["date_fin"] : null,
    isset($_GET["limite"])? $_GET["limite"] : null
);

echo("[\n");
$premier = true;
foreach($conges as $c){
    if($premier)
        $premier = false;
    else
        echo(",");
?>
    {
        "date": "<?=$c->date?>",
        "ts": <?=(new DateTime($c->date))->getTimestamp()?>,
        "login": "<?=strtr($c->login, $echappement)?>"
    }
<?php
}
echo("]\n");
?>
