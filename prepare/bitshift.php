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

class bitshift extends instruction {
    protected $left = null;
    protected $operator = null;
    protected $right = null;
    
    function __construct($expression = null) {
        parent::__construct(array());

        $this->left = $expression[0];
        $this->operator = $this->makeToken_traite($expression[1]);
        $this->right = $expression[2];
    }

    function __toString() {
        return __CLASS__." ".$this->left." "." ".$this->operator." "." ".$this->right." ";
    }

    function getRight() {
        return $this->right;
    }

    function getOperator() {
        return $this->operator;
    }

    function getLeft() {
        return $this->left;
    }

    function neutralise() {
        $this->left->detach();
        $this->operator->detach();
        $this->right->detach();
    }

    function getRegex(){
        return array('bitshift_regex',
                    );
    }

}

?>