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

class operation extends instruction {
    protected $droite = null;
    protected $operation = null;
    protected $gauche = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if (count($expression) == 3) {
            $this->droite = $expression[0];
            $this->operation = $this->makeToken_traite($expression[1]);
            $this->gauche = $expression[2];
        } else {
            $this->stopOnError("We shouldn't reach here");
        }
    }

    function __toString() {
        return __CLASS__." ".$this->droite." ".$this->operation." ".$this->gauche;
    }

    function getDroite() {
        return $this->droite;
    }

    function getOperation() {
        return $this->operation;
    }

    function getGauche() {
        return $this->gauche;
    }

    function neutralise() {
       $this->droite->detach();
       $this->operation->detach();
       $this->gauche->detach();
    }

    function getRegex(){
        return array('operation_multiplication_regex',
                     'operation_addition_regex');
    }
    
    function getToken() { return Token::ANY_TOKEN; }
}

?>