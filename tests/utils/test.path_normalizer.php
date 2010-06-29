<?php

include('../../libs/path_normaliser.php');
require_once 'PHPUnit/Framework.php'; 


class Path_Normalizer_Framework_TestCase  extends PHPUnit_Framework_TestCase {
    function __construct() {
    }
    
    function testAll() {
        $valeurs = array(',init.php' => 'init.php',
                         'a,../init.php' => 'init.php',
                         'a,init.php' => 'a/init.php',
                         'a/b,init.php' => 'a/b/init.php',
                         'a/b,../init.php' => 'a/init.php',
                         'a/b,../../init.php' => 'init.php',
                         'ManyWebServices/ManyWebServices.php,ManyWebServices/nusoap/nusoap.php' => 'ManyWebServices/nusoap/nusoap.php',
                         
                         );
        
        
        foreach($valeurs as $args => $wanted) {
            list($root, $path) = explode(',', $args); 
            
            $normalised = path_normaliser($root, $path);
            
            $this->assertEquals($normalised, $wanted);
        }
    }
    
}

?>