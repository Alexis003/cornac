<?php

include('prepare/token.php');
include('prepare/token_traite.php');
include('prepare/instruction.php');
include('prepare/analyseur_regex.php');
include('prepare/variable.php');

$includes = glob('prepare/*.php');
foreach($includes as $include) {
    if ($include == "prepare/instruction.php") { continue; }
    if ($include == "prepare/token.php") { continue; }
    if ($include == "prepare/analyseur_regex.php") { continue; }
    if ($include == "prepare/variable.php") { continue; }
    if ($include == "prepare/token_traite.php") { continue; }
    if ($include == "prepare/analyseur.php") { continue; }
    if ($include == "prepare/commun.php") { continue; }
    include($include);
}
?>