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

class _catch extends instruction {
    protected $exception = null;
    protected $variable = null;
    protected $block = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        $this->block = array_pop($expression);
        if (count($expression) == 2) {
            $this->exception = $this->make_token_traite($expression[0]);
            $this->variable  = $expression[1];
        } else {
            $this->stop_on_error("Unexpected number of arguments received : (".count($expression)." instead of 3) in ".__METHOD__);
        }
    }

    function __toString() {
        return __CLASS__." (".$this->exception." ".$this->variable.") ";
    }

    function getException() {
        return $this->exception;
    }

    function getVariable() {
        return $this->variable;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        $this->exception->detach();
        $this->variable->detach();
        $this->block->detach();
    }

    function getRegex(){
        return array('catch_normal_regex',
                    );
    }

}

?>