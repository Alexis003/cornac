<?php

$php = file_get_contents('prepare/template.tree.php');

preg_match_all("/(affiche_.*?)\(/", $php, $r);
//print_r($r[1]);

$liste = $r[1];

$php = file_get_contents('prepare/template.dot.php');

preg_match_all("/(affiche_.*?)\(/", $php, $r);
//print_r($r[1]);

$liste_dot = $r[1];

$manque = array_diff($liste, $liste_dot);
print_r($manque);
print count($manque)." reste à faire\n";
?>