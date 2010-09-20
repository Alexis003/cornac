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

class _declare extends instruction {
    protected $ticks = null;
    protected $encoding = null;
    protected $block = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if ($expression[count($expression) - 1]->checkClass('block')) {
            $this->block = array_pop($expression);
        } else {
            // @empty_else
        }
        
        if ($expression[0]->checkClass('parentheses')) {
            // @doc we expect no initialisation 
            if (!$this->set(strtolower($expression[0]->getContenu()->getVariable()->getCode()), 
                            $expression[0]->getContenu()->getValeur())) {
                $this->stop_on_error($expression[0]->getContenu()->getVariable()." is unknown in ".__METHOD__."\n");
            }
        } elseif ($expression[0]->checkClass('arginit')) {
            // @doc we expect an initialisation 
            if (!$this->set(strtolower($expression[0]->getVariable()->getCode()), 
                            $expression[0]->getValeur())) {
                $this->stop_on_error($expression[0]->getVariable()." is unknown in ".__METHOD__."\n");
            }
            if (!$this->set(strtolower($expression[1]->getVariable()->getCode()), 
                            $expression[1]->getValeur())) {
                stop_on_error($expression[1]->getVariable()." is unknown in ".__METHOD__."\n");
            }
        } else {
            $this->stop_on_error("Entree is of unexpected class ".get_class($expression[0])." in ".__METHOD__."\n");
        }
    }
    
    function set($nom, $valeur) {
        if (in_array($nom, array('ticks','encoding'))) {
            $this->$nom = $valeur;
            return true;
        }
        return false;
    }

    function __toString() {
        $string = __CLASS__." ticks= ".$this->tick." encoding = ".$this->encoding;
        if (!is_null($this->block)) { $string .= " ".$this->block;}
        return $string; 
    }

    function getTicks() {
        return $this->ticks;
    }

    function getEncoding() {
        return $this->encoding;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        if (!is_null($this->ticks)) {
            $this->ticks->detach();
        }
        if (!is_null($this->encoding)) {
            $this->encoding->detach();
        }
        if (!is_null($this->block)) {
            $this->block->detach();
        }
    }

    function getRegex(){
        return array('declare_normal_regex',
                     'declare_alternative_regex',
                    );
    }

}

?>