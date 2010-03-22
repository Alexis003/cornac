<?php

$args = $argv;
array_shift($args);

$classe = $args[0];
if ($classe + 0 != 0) { 
    print "$classe ne semble pas être un nom de classe\n";
    die(); 
}
if (isset($args[1])) {
    $methodes = $args[1];
} else {
    $methodes = 1;
}

if (!file_exists('scripts/'.$classe.'.1.test.php')) {
    print "La classe $classe ne semble pas avoir de scripts associés\n";
    die();
}

if (file_exists('class.'.$classe.'.test.php')) {
    $code = file_get_contents('class.'.$classe.'.test.php');
    preg_match_all("#test{$classe}(\d+)#i", $code, $r);
    
    $methodes += count($r[1]);
}

if ($methodes < 1) { $methodes = 1; }
$Classe = ucfirst($classe);

$scripts = glob('scripts/'.$classe.'.*');
if (count($scripts) < $methodes)  {
    print "Il semble manquer de ".($methodes - count($scripts))." scripts de tests\n";
} elseif (count($scripts) > $methodes)  {
    print "Il semble manquer de ".(count($scripts) - $methodes)." methodes de tests\n";
} else {
    // OK
}



$code = "<?"."php";
$code .= <<<PHP

include_once('Analyseur_Framework_TestCase.php');

class {$Classe}_Test extends Analyseur_Framework_TestCase
{
    /* $methodes methodes */

PHP;

for($i = 1; $i < $methodes + 1; $i++) {
    $code .= "    public function test{$Classe}$i()  { \$this->generic_test('{$classe}.$i'); }\n";
}

$code .= '
}

?'.'>';

file_put_contents('class.'.$classe.'.test.php', $code);

// test alltests
$code = file_get_contents('alltests.php');

if (preg_match('#\'class.'.$classe.'.test.php\',#is', $code, $r)) {
    print "'class.$classe.test.php' est déjà dans le alltests\n";
} else {
    print "ajout de class.$classe.test.php dans le alltests\n";
    $code = str_replace('// Prochain tests',"'class.$classe.test.php',\n// Prochain tests",$code);
    file_put_contents('alltests.php',$code);
}

//


?>