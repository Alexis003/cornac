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

class template_left extends template {
    protected $root = null;
    protected $stats = 0;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function display($node = null, $niveau = 0) {
        if (is_null($node)) {
            $node = $this->root;
        }
        
        if (!is_object($node)) {
            print "Fatal : attemptint to display a non-object in ".__METHOD__."\n\n";
            debug_print_backtrace();
            die();
        }
        $class = get_class($node);
        $method = "display_$class";

        if (method_exists($this, $method)) {
            $this->$method($node, $niveau + 1);
        } else {
            $this->stats['missing'][$method] = 1;
        }
        if (!is_null($node->getNext())){
            $this->display($node->getNext(), $niveau);
        } else {
            if ($niveau == 0) {
                if (count($this->stats['missing']) == 0) { 
                    unset($this->stats['missing']); 
                }
            }
        }
    }
    
    function display_arglist($node, $niveau) {
        
        $elements = $node->getList();
        if (is_array($elements)) {
            foreach($elements as $id => $e) {
                $this->display($e, $niveau + 1);
            }
        }
    }

    function display_affectation($node, $niveau) {
        
        $this->display($node->getLeft(), $niveau + 1);
        $this->display($node->getRight(), $niveau + 1);
    }

    function display_block($node, $niveau) {
        
        $elements = $node->getList();
        foreach($elements as $id => $e) {
            $this->display($e, $niveau + 1);
        }
    }

    function display__break($node, $niveau) {
        
    }

    function display_comparison($node, $niveau) {
        
        $this->display($node->getLeft(), $niveau + 1);
        $this->display($node->getRight(), $niveau + 1);
    }
    
    function display_ternaryop($node, $niveau) {
        
        $this->display($node->getCondition(), $niveau + 1);
        $this->display($node->getThen(), $niveau + 1);
        $this->display($node->getElse(), $niveau + 1);
    }

    function display_codephp($node, $niveau) {
        
        $this->display($node->getphp_code(), $niveau + 1);
    }

    function display_concatenation($node, $niveau) {
        
        $elements = $node->getList();
        foreach($elements as $id => $e) {
            $this->display($e, $niveau + 1);
        }
    }

    function display_constante($node, $niveau) {
        
    }

    function display__for($node, $niveau) {
        
        $this->display($node->getInit(), $niveau + 1);
        $this->display($node->getEnd(), $niveau + 1);
        $this->display($node->getIncrement(), $niveau + 1);
        $this->display($node->getBlock(), $niveau + 1);
    }

    function display__foreach($node, $niveau) {
        

        $gets = array('getArray','getKey','getValue','getBlock');

        foreach($gets as $get) {
            $list = $node->$get();
            if (!is_null($list)) {
                $this->display($list, $niveau + 1);
            }
        }
    }

    function getKey() {
        return $this->key;
    }

    function getValue() {
        return $this->value;
    }

    function getBlock() {    }

    function display_functioncall($node, $niveau) {
        

        $args = $node->getArgs();
        $this->display($args, $niveau + 1);
    }

    function display_ifthen($node, $niveau) {
        
        $conditions = $node->getCondition();
        $thens = $node->getThen();
        foreach($conditions as $id => $condition) {
            $this->display($condition, $niveau + 1);
            $this->display($thens[$id], $niveau + 1);
        }
        if (!is_null($node->getElse())){
            $this->display($node->getElse(), $niveau + 1);
        }
    }

    function display_inclusion($node, $niveau) {
        
        $inclusion = $node->getInclusion();
        $this->display($inclusion, $niveau + 1);
    }

    function display_logical($node, $niveau) {
        
        $this->display($node->getLeft(), $niveau + 1);
        $this->display($node->getOperator(), $niveau + 1);
        $this->display($node->getRight(), $niveau + 1);
    }

    function display_literals($node, $niveau) {
        
    }

    function display_method($node, $niveau) {
        
        $this->display($node->getObject(), $niveau + 1);
        $this->display($node->getMethod(), $niveau + 1);
    }

    function display__new($node, $niveau) {
         print str_repeat('  ', $niveau).' new '.$node->getClass()." ".$node->getArgs()." \n";
    }

    function display_noscream($node, $niveau) {
        
        $this->display($node->getExpression(), $niveau + 1);
    }

    function display_opappend($node, $niveau) {
        
        $this->display($node->getVariable(), $niveau + 1);
    }

    function display_operation($node, $niveau) {
        
        $this->display($node->getLeft(), $niveau + 1);
        $this->display($node->getRight(), $niveau + 1);
    }

    function display_parenthesis($node, $niveau) {
        
    }

    function display_preplusplus($node, $niveau) {
        
        $this->display($node->getOperator(), $niveau + 1);
        $this->display($node->getVariable(), $niveau + 1);
    }
    
    function display_property($node, $niveau) {
        
    }

    function display_postplusplus($node, $niveau) {
        
        $this->display($node->getOperator(), $niveau + 1);
        $this->display($node->getVariable(), $niveau + 1);
    }

    function display_rawtext($node, $niveau) {
        
    }

    function display_sequence($node, $niveau) {
        
        $elements = $node->getElements();
        foreach($elements as $id => $e) {
            $this->display($e, $niveau + 1);
        }
    }

    function display__array($node, $niveau) {
        
        $this->display($node->getVariable(), $niveau + 1);
        $this->display($node->getIndex(), $niveau + 1);
    }

    function display_variable($node, $niveau) {
        
    }

    function display_processedToken($node, $niveau) {
        
    }

    function display__while($node, $niveau) {
        
        $this->display($node->getCondition(), $niveau + 1);
        $this->display($node->getBlock(), $niveau + 1);
    }

    function display_Token($node, $niveau) {
        print $node."".$node->getId()."\n";
        $this->stats++;
    }

}

?>