<?php

define('OPT_NO_VALUE',false);
define('OPT_WITH_VALUE',true);

$args = $argv;

$prefixe = getOption($args, '-I', OPT_WITH_VALUE, null);
$module = getOption($args, '-a', OPT_WITH_VALUE,  null);
$fichier = getOption($args, '-f', OPT_WITH_VALUE, null);
define('VERBOSE', getOption($args, '-v', null, OPT_NO_VALUE));

if (VERBOSE) {
    print "Travail avec la base $prefixe\n";
    print "Travail avec le module $module\n";
    print "Travail avec le fichier $fichier\n";
}

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

$requete = 'SELECT * FROM '.$prefixe.'_rapport WHERE module='.$mysql->quote($module);
if (!empty($fichier)) {
    $requete .= ' AND fichier='.$mysql->quote($fichier);
}
$res = $mysql->query($requete);

if (!$res) {
    print "<document />";
    die();
}
$lignes = $res->fetchall(PDO::FETCH_ASSOC);

$fin = "";

$fin .= "<document>\n";
foreach($lignes as $id => $ligne) {
    $fin .= "  <ligne id=\"$id\">\n";
    foreach($ligne as $col => $valeur) {
        $fin .= "    <$col>".htmlentities($valeur)."</$col>\n";
    }
    $fin .= "  </ligne>\n";
}
$fin .= "</document>\n";

print $fin;

function getOption(&$args, $option, $value = true, $default = null) {
    if ($id = array_search($option, $args)) {
        unset($args[$id]);
        
        if ($value) {
            $prefixe = $args[$id + 1];
            unset($args[$id + 1]);
        } else {
            $prefixe = true;
        }
    } else {
        if ($value) {
            $prefixe = $default;
        } else {
            $prefixe = false;
        }
    }
    
    return $prefixe;
}

?>