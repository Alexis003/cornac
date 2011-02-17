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

class arginit extends Cornac_Tokenizeur_Token_Instruction {
    protected $tname = 'arginit';
    protected $variable = array();
    protected $value = null;

    function __construct($expression) {
        parent::__construct(array());

        $this->variable = $expression[0];
        $this->value = $expression[1];
    }
    
    function __toString() {
        return $this->getTname()." ".$this->variable." = ".$this->value." ";
    }

    function getVariable() {
        return $this->variable;
    }

    function getValue() {
        return $this->value;
    }

    function neutralise() {
        $this->variable->detach();
        $this->value->detach();
    }

    function getRegex() {
        return array(
    'arginit_literal_regex',
    'arginit_array_regex',
);
    }
}

?>