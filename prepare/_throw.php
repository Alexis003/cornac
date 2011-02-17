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

class _throw extends Cornac_Tokenizeur_Token_Instruction {
    protected $tname = '_throw';
    protected $exception = null;
    
    function __construct($expression = null) {
        parent::__construct(array());

        $this->exception = $expression[0];
    }

    function __toString() {
        return $this->getTname()." ".$this->exception;
    }

    function getException() {
        return $this->exception;
    }

    function neutralise() {
        $this->exception->detach();
    }

    function getRegex(){
        return array('throw_regex',
                     'throw_parenthesis_regex',
                    );
    }

}

?>