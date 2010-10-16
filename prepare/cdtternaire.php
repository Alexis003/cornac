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

class cdtternaire extends instruction {
    protected $condition = null;
    protected $vraie = null;
    protected $faux = null;
    
    function __construct($expression) {
        parent::__construct(array());
        if (!is_array($expression)) {
        
        } elseif (count($expression) == 2) {
            if ($expression[0]->checkClass('arglist')) {
                $this->condition = $expression[0]->getList();
                $this->condition = $this->condition[0];
            } else {
                $this->condition = $expression[0];
            }
            $this->vraie     = null;
            $this->faux      = $expression[1];
        } elseif (count($expression) == 3) {
            if ($expression[0]->checkClass('arglist')) {
                $this->condition = $expression[0]->getList();
                $this->condition = $this->condition[0];
            } else {
                $this->condition = $expression[0];
            }
            $this->vraie     = $expression[1];
            $this->faux      = $expression[2];
        } else {
            $this->stopOnError("Wrong number of arguments  : '".count($expression)."' in ".__METHOD__);
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    static function getRegex() {
        return array('cdtternaire_normal_regex');
    }

    function getCondition() {
        return $this->condition;
    }

    function getVraie() {
        return $this->vraie;
    }

    function getFaux() {
        return $this->faux;
    }

    function neutralise() {
        $this->condition->detach();
        if (!is_null($this->vraie)) {
            $this->vraie->detach();
        }
        $this->faux->detach();
    }
}
?>