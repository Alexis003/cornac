<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

$args = $GLOBALS['argv'];

require_once 'PHPUnit/Framework.php'; 

$debut = microtime(true);
if (in_array('-f', $args)) {
    print "\nFull analysis of test files\n";
    $shell = <<<SHELL
cd ../..
./tokinit.php -r -d ./tests/auditeur/scripts/ -I testsunitaires -K -g mysql,cache
./tokclient.php -I testsunitaires
cd auditeur
./auditeur.php -d -p tu -I testsunitaires
SHELL;
    $retour = shell_exec($shell);
    $fin = microtime(true);
    print "  Done (".number_format(($fin - $debut), 2)." s)\n";
}

if (in_array('-a', $args)) {
    print "\nFull update of auditeur's tasks (No tokenizeur used)\n";
    $shell = <<<SHELL
cd ../../auditeur
./auditeur.php -d -p tu -I testsunitaires
SHELL;
    $retour = shell_exec($shell);
    $fin = microtime(true);
    print "  Done (".number_format(($fin - $debut), 2)." s)\n";
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
            $this->assertFalse(true, "Script scripts/".$this->prefix.".php doesn't compile\n");
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
            $elements = array_map('addslashes', $elements);
            $this->assertTrue(false, "".count($elements)." objects were found, but they are not processed by the tests ('".join("', '", $elements)."')");
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
            $this->assertFalse(true, "Script scripts/".$this->prefix.".php doesn't compile\n");
}

        $shell = <<<SHELL
cd ../../auditeur
./reader.php -I testsunitaires -F xml -a {$this->prefix} -f ./tests/auditeur/scripts/{$this->prefix}.php
SHELL;

        $retour = shell_exec($shell);
        $this->assertTrue(!empty($retour),'reader is empty');
        $this->assertTrue(strpos($retour, 'Usage : ') === false);

        file_put_contents('log/'.$this->prefix.'.log', $retour);

        $sx = simplexml_load_string($retour);

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
            $elements = array_map('addslashes', $elements);
            $this->assertTrue(false, "".count($elements)." objects were found, but they are not processed by the tests ('".join("', '", $elements)."')");
        }
    }
}

?>