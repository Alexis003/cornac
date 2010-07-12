<?php

class Cornac_Framework_TestCase extends PHPUnit_Framework_TestCase {
    public function test_help() {
        $shell = <<<SHELL
cd ../..; ./cornac.php -h
SHELL;

        $retour = shell_exec($shell);
        
        $this->assertContains( '-?', $retour);
        $this->assertContains( '-d', $retour);
        $this->assertContains( '-o', $retour);
        $this->assertContains( '-s', $retour);
    }
    
    public function test_empty() {
        $shell = <<<SHELL
cd ../..; ./cornac.php
SHELL;

        $retour = shell_exec($shell);
        $this->assertContains( 'Usage : ./cornac -d <source folder> -o <output folder>', $retour);
    }

    public function test_destination() {
        $shell = <<<SHELL
cd ../..; ./cornac.php -d ./tests/cornac/appli/
SHELL;

        $retour = shell_exec($shell);
        $this->assertContains( 'Usage : ./cornac -d <source folder> -o <output folder>', $retour);
    }
}

?>