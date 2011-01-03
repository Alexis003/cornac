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

class template_hierarchic extends template {
    protected $root = null;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function display($node = null, $niveau = 0) {
        if (is_null($node)) {
            $node = $this->root;
        }
        
        if (!is_object($node)) {
            debug_print_backtrace();
            die();
        }
        $class = get_class($node);
        $method = "display_$class";

        $this->$method($node, $niveau + 1);
        if (!is_null($node->getNext())){
            $this->display($node->getNext(), $niveau);
        }
    }
    
    function display_arglist($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $elements = $node->getList();
        foreach($elements as $id => $e) {
            $this->display($e, $niveau + 1);
        }
    }

    function display_affectation($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $this->display($node->getLeft(), $niveau + 1);
        $this->display($node->getRight(), $niveau + 1);
    }
    
    function display_ternaryop($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $this->display($node->getCondition(), $niveau + 1);
        $this->display($node->getThen(), $niveau + 1);
        $this->display($node->getElse(), $niveau + 1);
    }

    function display_codephp($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $this->display($node->getphp_code(), $niveau + 1);
    }

    function display_concatenation($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $elements = $node->getList();
        foreach($elements as $id => $e) {
            $this->display($e, $niveau + 1);
        }
    }

    function display_constante($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
    }

    function display_functioncall($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";

        $args = $node->getArgs();
        $this->display($args, $niveau + 1);
    }

    function display_inclusion($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $inclusion = $node->getInclusion();
        $this->display($inclusion, $niveau + 1);
    }

    function display_literals($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
    }

    function display_operation($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $this->display($node->getLeft(), $niveau + 1);
        $this->display($node->getRight(), $niveau + 1);
    }

    function display_parenthesis($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
    }

    function display_rawtext($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
    }

    function display_sequence($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $elements = $node->getElements();
        foreach($elements as $id => $e) {
            $this->display($e, $niveau + 1);
        }
    }

    function display__array($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
        $this->display($node->getVariable(), $niveau + 1);
        $this->display($node->getIndex(), $niveau + 1);
    }

    function display_variable($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." \n";
    }

    function display_Token($node, $niveau) {
        print str_repeat('  ', $niveau).get_class($node)." ".$node->getCode()." ( Affichage par défaut)\n";
    }

}

?>