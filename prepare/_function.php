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

class _function extends instruction {
    protected $name = '';
    protected $_abstract = null;
    protected $_static = null;
    protected $_visibility = null;
    protected $reference = null;
    protected $args = null;
    protected $block = null;
    

    function __construct($expression) {
        parent::__construct(array());
        
        while($expression[0]->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_ABSTRACT, T_FINAL))) {

            if ($expression[0]->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE))) {
                $this->_visibility = $this->make_token_traite($expression[0]);
                unset($expression[0]);
                $expression = array_values($expression);
                continue;
            }

            if ($expression[0]->checkToken(array(T_STATIC))) {
                $this->_static = $this->make_token_traite($expression[0]);

                unset($expression[0]);
                $expression = array_values($expression);
                continue;
            }

            if ($expression[0]->checkToken(array(T_ABSTRACT, T_FINAL))) {
                $this->_abstract = $this->make_token_traite($expression[0]);

                unset($expression[0]);
                $expression = array_values($expression);
                continue;
            }
            
            $this->stop_on_error("On ne devrait pas arriver ici : ".__CLASS__);
        }

        if (count($expression) == 3) {
            $this->name = $expression[0];
            $this->args = $expression[1];
            $this->block = $expression[2];
        } elseif (count($expression) == 4) {
            $this->reference = $expression[0];
            $this->name = $expression[1];
            $this->args = $expression[2];
            $this->block = $expression[3];
        } else {
            $this->stop_on_error("Wrong number of arguments  : '".count($expression)."' in ".__METHOD__);
        }
        
        if ($this->block->getCode() == ';') {
            $this->block     = new block();
        }
    }
    
    function __toString() {
        return __CLASS__." function ".$this->name." (".$this->args.") {".$this->block."} ";
    }

    function getName() {
        return $this->name;
    }

    function getReference() {
        return $this->reference;
    }

    function getArgs() {
        return $this->args;
    }

    function getBlock() {
        return $this->block;
    }

    function getVisibility() {
        return $this->_visibility;
    }

    function getAbstract() {
        return $this->_abstract;
    }

    function getStatic() {
        return $this->_static;
    }

    function neutralise() {
        $this->name->detach();
        if (!is_null($this->reference)) {   
            $this->reference->detach();
        }
        if (!is_null($this->_visibility)) {   
            $this->_visibility->detach();
        }
        if (!is_null($this->_static)) {   
            $this->_static->detach();
        }
        if (!is_null($this->_abstract)) {   
            $this->_abstract->detach();
        }
        $this->args->detach();
        $this->block->detach();
    }

    function getRegex() {
        return array(
    'function_simple_regex',
    'function_reference_regex',
    'function_abstract_regex',
    'function_typehint_regex',
    'function_typehintreference_regex',
);
    }
}

?>