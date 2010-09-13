#!/usr/bin/php
<?php

if ($id = array_search('-f', $argv)) {
    unset($argv[$id]);
    define('CREATE', true);
} else {
    define('CREATE', false);
}

array_shift($argv);
$args = $argv;

foreach($args as $arg) {
    if (!file_exists("./scripts/".$arg.".test.php")) {
        $module = glob("./scripts/".$arg.".*.test.php");
        if (count($module) == 0) {
            print "Le script d'exécution './scripts/".$arg.".test.php' n'existe pas\n";
            die();
        }
        
        foreach($module as $m) {
            preg_match("#./scripts/(".$arg."\.\d*?)\.test.php#", $m, $r);
            
            print "./test_update.php -f $r[1]\n";
            shell_exec("./test_update.php -f $r[1]");
            
        }
        die();
    }
    
    if (!file_exists("./exp/".$arg.".test.exp")) {
        if (!CREATE) {
            print "Le script de resultat './exp/".$arg.".test.exp' n'existe pas\n";
//            die();
        }
    }
    
    if (CREATE) {
      print "Modification de /exp/".$arg.".test.exp\n";
      shell_exec("cd ../../; ./tokenizeur.php -f tests/tokenizeur/scripts/".$arg.".test.php -I testsunitaires -g tree > tests/tokenizeur/exp/".$arg.".test.exp");

      $fichier = "exp/".$arg.".test.exp";
      $exp = file_get_contents($fichier);
      $exp = str_replace("Fichier de directives : ini/tokenizeur.ini\n", '', $exp);
      $exp = str_replace("Directives files : \n", '', $exp);
      $exp = str_replace("No more tasks to work on. Finishing.\n",'', $exp);

      file_put_contents($fichier, $exp);
    } else {
      shell_exec("bbedit ./exp/".$arg.".test.exp");
      shell_exec("cd ../../; ./tokenizeur.php -f tests/tokenizeur/scripts/".$arg.".test.php  -I testsunitaires -g tree | bbedit");
    }
}

?>