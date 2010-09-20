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

class affectation extends instruction {
    protected $_static    = null;
    protected $_visibility    = null;
    protected $droite    = null;
    protected $operateur = null;
    protected $gauche    = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if (is_array($expression)) {
            while ($expression[0]->checkToken(array(T_PUBLIC, T_PRIVATE, T_PROTECTED, T_STATIC))) {
                if ($expression[0]->checkToken(array(T_PUBLIC, T_PRIVATE, T_PROTECTED))) {
                    $this->_visibility = $this->make_token_traite($expression[0]);
                } elseif ($expression[0]->checkToken(array(T_STATIC))) {
                    $this->_static = $this->make_token_traite($expression[0]);
                }

                unset($expression[0]);
                $expression = array_values($expression);
            }

            if (count($expression) != 3) {
                $this->stop_on_error("Affectation with unexpected number of valudes : ".count($expression)." received\n");
            }

            $this->droite = $expression[0];
            $expression[1]->setLine($expression[0]->getLine());
            $this->operateur = $this->make_token_traite($expression[1]);
            $this->gauche = $expression[2];
            $this->setLine($this->droite->getLine());
        } else {
            $this->stop_on_error("Affectation received strange number of values : ".count($expression)." received\n");
        }
    }

    function __toString() {
        return __CLASS__." ".$this->droite." ".$this->operateur." ".$this->gauche;
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

    function getVisibility() {
        return $this->_visibility;
    }

    function getStatic() {
        return $this->_static;
    }

    function getToken() {
        return 0;
    }

    function neutralise() {
        if (!is_null($this->_visibility)) {
            $this->_visibility->detach();
        }
        if (!is_null($this->_static)) {
            $this->_static->detach();
        }
        $this->droite->detach();
        $this->operateur->detach();
        $this->gauche->detach();
    }

    function getRegex(){
        return array('affectation_normal_regex', 
                     'affectation_avecpointvirgule_regex', 
                     'affectation_list_regex',
                    );
    }
}

?>