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

class xxxxxx extends instruction {
    protected $condition = null;
    protected $then = null;
    protected $else = null;
    
    function __construct($condition, $then, $else) {
        parent::__construct(array());
        
        $this->condition = $condition;
        $this->then = $then;
        $this->else = $else;
        
    }

    function __toString() {
        return __CLASS__." if (".$this->condition.") then ".$this->then." else ".$this->else;
    }

    function getCondition() {
        return $this->condition;
    }

    function getThen() {
        return $this->then;
    }

    function getElse() {
        return $this->else;
    }
    
    function getRegex() {
        return array();
    }

}

?>