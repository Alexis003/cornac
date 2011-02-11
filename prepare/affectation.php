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

class affectation extends instruction {
    protected $left           = null;
    protected $operator       = null;
    protected $right          = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if (count($expression) != 3) {
            $this->stopOnError("Affectation with unexpected number of values : ".count($expression)." received\n");
        }

        $this->left = $expression[0];
        $expression[1]->setLine($expression[0]->getLine());
        $this->operator = $this->makeProcessedToken('_affectation_', $expression[1]);
        $this->right = $expression[2];
        $this->setLine($this->left->getLine());
    }

    function __toString() {
        return __CLASS__." ".$this->left." ".$this->operator." ".$this->right;
    }

    function getLeft() {
        return $this->left;
    }

    function getOperator() {
        return $this->operator;
    }

    function getRight() {
        return $this->right;
    }

    function getToken() {
        return 0;
    }

    function neutralise() {
        $this->left->detach();
        $this->operator->detach();
        $this->right->detach();
    }

    function getRegex(){
        return array('affectation_normal_regex', 
                     'affectation_withsemicolon_regex', 
                     'affectation_list_regex',
                    );
    }
}

?>