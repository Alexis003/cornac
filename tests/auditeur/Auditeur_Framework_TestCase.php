<?php

$args = $GLOBALS['argv'];

require_once 'PHPUnit/Framework.php'; 

$debut = microtime(true);
if (in_array('-f', $args)) {
    print "\nAnalyse des nouveaux fichiers\n";
    $shell = <<<SHELL
cd ../..
./tokenizeur.php -r -d ./tests/auditeur/scripts/ -g mysql,cache -I testsunitaires
cd auditeur
./auditeur.php -d -p tu -I testsunitaires
SHELL;
    $retour = shell_exec($shell);
    $fin = microtime(true);
    print "  Faite (".number_format(($fin - $debut), 2)." s)\n";
}

class Auditeur_Framework_TestCase  extends PHPUnit_Framework_TestCase {
    protected $prefix="Auditeur_Framework_TestCase";

    function __construct() {
        $this->prefix = substr(get_class($this), 0, -5);
    }
    
    protected function generic_test() {
        $shell = <<<SHELL
php -l ./scripts/{$this->prefix}.php
SHELL;

        $retour = shell_exec($shell);
        if ($retour != "No syntax errors detected in ./scripts/".$this->prefix.".php
") {
            $this->assertFalse(true, " le script scripts/".$this->prefix.".php ne compile pas\n");
}

        $shell = <<<SHELL
cd ../../auditeur
./reader.php -I testsunitaires -a {$this->prefix} -f ./tests/auditeur/scripts/{$this->prefix}.php
SHELL;

        $retour = shell_exec($shell);
        file_put_contents('log/'.$this->prefix.'.log', $retour);
        // @note first log, then process.
        
        $sx = simplexml_load_string($retour);
        
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
php -l ./scripts/{$this->prefix}.php
SHELL;

        $retour = shell_exec($shell);
        if ($retour != "No syntax errors detected in ./scripts/".$this->prefix.".php
") {
            print $retour; 
            $this->assertFalse(true, " le script scripts/".$this->prefix.".php ne compile pas\n");
}

        $shell = <<<SHELL
cd ../../auditeur
./reader.php -I testsunitaires -F xml -a {$this->prefix} -f ./tests/auditeur/scripts/{$this->prefix}.php
SHELL;

        $retour = shell_exec($shell);
        $this->assertTrue(!empty($retour),'reader is empty');
        $this->assertTrue(strpos($retour, 'Usage : ') === false);
        $sx = simplexml_load_string($retour);
        
        file_put_contents('log/'.$this->prefix.'.log', $retour);
        
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