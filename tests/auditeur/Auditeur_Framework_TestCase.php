<?php

require_once 'PHPUnit/Framework.php'; 
//include_once('Phpunit_Framework_TestCase.php');

print "\nAnalyse des nouveaux fichiers\n";
$debut = microtime(true);
$shell = <<<SHELL
cd ../..
./tokenizeur.php -r -d ./tests/auditeur/scripts/ -g mysql,cache -I testsunitaires
cd auditeur
php auditeur.php tu
SHELL;
$retour = shell_exec($shell);
$fin = microtime(true);
print "  Faite (".number_format(($fin - $debut), 2)." s)\n";

class Auditeur_Framework_TestCase  extends PHPUnit_Framework_TestCase {

    protected function generic_test() {
        $shell = <<<SHELL
php -l ./scripts/{$this->name}.php
SHELL;

        $retour = shell_exec($shell);
        if ($retour != "No syntax errors detected in ./scripts/".$this->name.".php
") {
            $this->assertFalse(true, " le script /".$this->name." ne compile pas\n");
}

        $shell = <<<SHELL
cd ../../auditeur
php lecture_module.php -I tu -a {$this->name} -f ./tests/auditeur/scripts/{$this->name}.php
SHELL;

        $retour = shell_exec($shell);
        $sx = simplexml_load_string($retour);
        
        file_put_contents('log/'.$this->name.'.log', $retour);
        
        $elements = array();
        foreach($sx as $element) {
            $elements[$element->element.""] = $element->element."";
        }

        foreach($this->attendus as $attendu) {
            $this->assertTrue(in_array($attendu, $elements), "$attendu n'a pas été trouvé mais le devrait\n");
            unset($elements[$attendu]);
        }

        foreach($this->inattendus as $inattendu) {
            $this->assertTrue(!in_array($inattendu, $elements), "$inattendu a été trouvé mais ne devrait pas\n");
            unset($elements[$inattendu]);
        }
        
        if (!empty($elements)) {
            $this->assertTrue(false, "Il reste ".count($elements)." qui ne sont pas attendus ou inattendus (".join(', ', $elements).")");
        }

/*
        foreach($this->inattendus as $inattendu) {
            $this->assertTrue(!in_array($inattendu, $elements), "$inattendu a été trouvé mais ne devrait pas\n");
            unset($inattendu);
        }
*/
    }
}

?>