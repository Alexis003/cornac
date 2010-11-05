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


abstract class template {

    function __construct() { }
    
    abstract function display($node = null, $niveau = 0); 
}

class tree extends template {
    protected $root = null;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function display($node = null, $niveau = 0) {
        if (is_null($node)) {
            $node = $this->root;
        }
        
        $class = get_class($node);
        $method = "display_$class";
        
        if (method_exists($this, $method)) {
            $this->$method($node, $niveau + 1);
        } else {
            print "Affichage tree de '".$method."'\n";die;
        }
        if (!is_null($node->getNext())){
            $this->display($node->getNext(), $niveau);
        }
        
        
    }

    function display_affectation($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        print str_repeat('  ', $niveau)."left : \n";
        $this->display($node->getLeft(), $niveau + 1);
        print str_repeat('  ', $niveau)."right : \n";
        $this->display($node->getRight(), $niveau + 1);
    }

    function display_codephp($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." ".$node->getCode()."\n";
        print str_repeat('  ', $niveau)."code : \n";
        $this->display($node->getphp_code(), $niveau + 1);
    }

    function display_literals($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." ".$node->getCode()."\n";
    }

    function display_operation($node, $niveau) {
         print str_repeat('  ', $niveau).__CLASS__." \n";
         print str_repeat('  ', $niveau)."left : \n";
         $this->display($node->getLeft(), $niveau + 1);
         print str_repeat('  ', $niveau)."operation : ".$node->getOperation()."\n";
         print str_repeat('  ', $niveau)."right : \n";
         $this->display($node->getRight(), $niveau + 1);
    }
    
    function display_sequence($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $elements = $node->getElements();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->display($e, $niveau + 1);
        }
    }

    function display__array($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." ".$node->getCode()."\n";
        $this->display($node->getVariable(), $niveau + 1);
        $this->display($node->getIndex(), $niveau + 1);
    }

    function display_variable($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." ".$node->getCode()."\n";
    }
    
    function display_Token($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." ".$node->getCode()." ( Affichage par défaut)\n";
    }

}

?>