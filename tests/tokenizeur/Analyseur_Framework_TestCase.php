<?php

class Analyseur_Framework_TestCase  extends PHPUnit_Framework_TestCase {

    protected function generic_test($test) {
        if (!file_exists('scripts/'.$test.'.test.php')) {
            print "\nLe script de tests 'scripts/$test.test.php' est manquant\n";
            $this->assertEquals(true, false);
        }
        $retour = shell_exec('cd ../../; ./tokenizeur.php -f tests/tokenizeur/scripts/'.$test.'.test.php -I testsunitaires -g tree');
        
        if (!file_exists('exp/'.$test.'.test.exp')) {
            print "\nLe fichier d'attendu 'exp/$test.test.exp' est manquant\n";
            $this->assertEquals(true, false);
        }
        $exp = file_get_contents('exp/'.$test.'.test.exp');
        $exp = str_replace('tests/tokenizeur/','tests/tokenizeur/scripts/', $exp);
        $exp = str_replace('scripts/scripts/','scripts/', $exp);

        $retour = preg_replace("/Fichier de directives : .*?\n/is",'', $retour);
        $retour = preg_replace("/Directives files : .*?\n/is",'', $retour);
        $retour = str_replace("No more tasks to work on. Finishing.\n",'', $retour);
        
        $this->assertEquals($retour, $exp);        
    }

}

?>