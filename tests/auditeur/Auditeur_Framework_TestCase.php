<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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

require_once 'PHPUnit/Autoload.php'; 

$debut = microtime(true);
if (in_array('-f', $args)) {
    print "\nFull analysis of test files\n";
    $shell = <<<SHELL
cd ../..
php bin/tokinit -r -d ./tests/auditeur/scripts/ -I testsunitaires -K -g mysql,cache
php bin/tokclient -I testsunitaires
php bin/auditeur -d -p tu -I testsunitaires
SHELL;
    $return = shell_exec($shell);

// @todo   $this->assertContains('Terminating', $return);

    $fin = microtime(true);
    print "  Done (".number_format(($fin - $debut), 2)." s)\n";
}

if (in_array('-a', $args)) {
    print "\nFull update of auditeur's tasks (No tokenizeur used)\n";
    $shell = <<<SHELL
cd ../..
php bin/auditeur -d -p tu -I testsunitaires
SHELL;
    $return = shell_exec($shell);
    
// @todo   $this->assertContains('Terminating', $return);
    
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

        $return = shell_exec($shell);
        if (strpos($return, "No syntax errors detected in ./scripts/".$this->prefix.".php") === false) {
            $this->assertFalse(true, "Script scripts/".$this->prefix.".php doesn't compile\n");
        }

        $sx = $this->read_log();

        $elements = array();
        foreach($sx as $element) {
            $elements[$element->element.""] = $element->element."";
        }

        foreach($this->expected as $expected) {
            $this->assertTrue(in_array($expected, $elements), "Couldn't find expected '$expected'\n");
            unset($elements[$expected]);
        }

        foreach($this->unexpected as $unexpected) {
            $this->assertTrue(!in_array($unexpected, $elements), "Found '$unexpected', but it shouldn't be\n");
            unset($elements[$unexpected]);
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

        $return = shell_exec($shell);
        if (strpos($return, "No syntax errors detected in ./scripts/".$this->prefix.".php") === false) {
            $this->assertFalse(true, "Script scripts/".$this->prefix.".php doesn't compile\n");
        }

        $sx = $this->read_log();

        $elements = array();
        foreach($sx as $element) {
            $elements[] = $element->element."";
        }

        foreach($this->expected as $expected) {
            $id = array_search($expected, $elements);
            $this->assertTrue($id !== false , "Couldn't find one of the expected '$expected'\n");
            unset($elements[$id]);
        }

        foreach($this->unexpected as $unexpected) {
            $id = array_search($expected, $elements);
            $this->assertTrue($id === false, "Found '$unexpected', but it shouldn't be\n");
            unset($elements[$id]);
        }
        
        if (!empty($elements)) {
            $elements = array_map('addslashes', $elements);
            $this->assertTrue(false, "".count($elements)." objects were found, but they are not processed by the tests ('".join("', '", $elements)."')");
        }
    }

    function read_log() {

        $shell = <<<SHELL
cd ../..
php bin/reader -I testsunitaires -F xml -a {$this->prefix} -f ./tests/auditeur/scripts/{$this->prefix}.php
SHELL;

        $return = shell_exec($shell);

        file_put_contents('log/'.$this->prefix.'.log', $return);
        $this->assertTrue(!empty($return),'reader didn\'t return anything');
        $this->assertTrue(strpos($return, 'Usage : ') === false,'reader returned a usage error');
        $this->assertContains('<?xml version="1.0" encoding="UTF-8"?>',$return); 
        $this->assertEquals(substr($return, 0, 38), '<?xml version="1.0" encoding="UTF-8"?'.'>', "reader didn'\t return valid XML."); 
        $this->assertNotEquals($return, '<?xml version="1.0" encoding="UTF-8"?'.'>
<document />', "Reader provided empty log");

        
        $sx = simplexml_load_string($return);
        
        return $sx;
    }
}

?>