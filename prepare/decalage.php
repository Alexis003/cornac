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

class decalage extends instruction {
    protected $gauche = null;
    protected $operateur = null;
    protected $droite = null;
    
    function __construct($expression = null) {
        parent::__construct(array());

        $this->gauche = $expression[0];
        $this->operateur = $this->make_token_traite($expression[1]);
        $this->droite = $expression[2];
    }

    function __toString() {
        return __CLASS__." ".$this->gauche." "." ".$this->operateur." "." ".$this->droite." ";
    }

    function getDroite() {
        return $this->droite;
    }

    function getOperateur() {
        return $this->operateur;
    }

    function getGauche() {
        return $this->gauche;
    }

    function neutralise() {
        $this->gauche->detach();
        $this->operateur->detach();
        $this->droite->detach();
    }

    function getRegex(){
        return array('decalage_regex',
                    );
    }

}

?>