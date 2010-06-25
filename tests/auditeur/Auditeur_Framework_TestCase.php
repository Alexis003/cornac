<?php

require_once 'PHPUnit/Framework.php'; 
//include_once('Phpunit_Framework_TestCase.php');

print "\nAnalyse des nouveaux fichiers\n";
$debut = microtime(true);
//./tokenizeur.php -r -d ./tests/auditeur/scripts/ -g mysql,cache -I testsunitaires
$shell = <<<SHELL
cd ../..
./tokenizeur.php -r -d ./tests/auditeur/scripts/ -g sqlite,cache -I testsunitaires
cd auditeur
./auditeur.php -p tu
SHELL;
$retour = shell_exec($shell);
$fin = microtime(true);
print "  Faite (".number_format(($fin - $debut), 2)." s)\n";

class Auditeur_Framework_TestCase  extends PHPUnit_Framework_TestCase {
    protected $name="Auditeur_Framework_TestCase";

    function __construct() {
    }
    
    protected function generic_test() {
        $shell = <<<SHELL
php -l ./scripts/{$this->name}.php
SHELL;

        $retour = shell_exec($shell);
        if ($retour != "No syntax errors detected in ./scripts/".$this->name.".php
") {
            $this->assertFalse(true, " le script scripts/".$this->name.".php ne compile pas\n");
}

        $shell = <<<SHELL
cd ../../auditeur
php lecture_module.php -I tu -p tu -a {$this->name} -f ./tests/auditeur/scripts/{$this->name}.php
SHELL;

        $retour = shell_exec($shell);
        $sx = simplexml_load_string($retour);
        
        file_put_contents('log/'.$this->name.'.log', $retour);
        
        $elements = array();
        foreach($sx as $element) {
            $elements[$element->element.""] = $element->element."";
        }

        foreach($this->attendus as $attendu) {
            $this->assertTrue(in_array($attendu, $elements), "Couldn't find expected '$attendu'\n");
            unset($elements[$attendu]);
        }

        foreach($this->inattendus as $inattendu) {
            $this->assertTrue(!in_array($inattendu, $elements), "Found '$inattendu', but it shouldn\'t be\n");
            unset($elements[$inattendu]);
        }
        
        if (!empty($elements)) {
            $this->assertTrue(false, "".count($elements)." objects were found, but they are not processed by the tests (".join(', ', $elements).")");
        }
    }

    protected function generic_counted_test() {
        $shell = <<<SHELL
php -l ./scripts/{$this->name}.php
SHELL;

        $retour = shell_exec($shell);
        if ($retour != "No syntax errors detected in ./scripts/".$this->name.".php
") {
            print $retour; 
            $this->assertFalse(true, " le script scripts/".$this->name.".php ne compile pas\n");
}

        $shell = <<<SHELL
cd ../../auditeur
php lecture_module.php -I tu -p tu -a {$this->name} -f ./tests/auditeur/scripts/{$this->name}.php
SHELL;

        $retour = shell_exec($shell);
        $sx = simplexml_load_string($retour);
        
        file_put_contents('log/'.$this->name.'.log', $retour);
        
        $elements = array();
        foreach($sx as $element) {
            $elements[] = $element->element."";
        }

        foreach($this->attendus as $attendu) {
            $id = array_search($attendu, $elements);
            $this->assertTrue($id !== false , "Couldn't find one of the expected '$attendu'\n");
            unset($elements[$id]);
        }

        foreach($this->inattendus as $inattendu) {
            $id = array_search($attendu, $elements);
            $this->assertTrue($id === false, "Found '$inattendu', but it shouldn\'t be\n");
            unset($elements[$id]);
        }
        
        if (!empty($elements)) {
            $this->assertTrue(false, "".count($elements)." objects were found, but they are not processed by the tests (".join(', ', $elements).")");
        }
    }
}

?>