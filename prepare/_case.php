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

class _case extends instruction {
    protected $condition = null;
    protected $block      = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        if ($expression[0]->checkToken(T_CASE)) {
            $this->condition = $expression[1];
            if (isset($expression[2])) {
                $this->block     = $expression[2];
            } else {
                $this->block     = new block();
            }
        } elseif ($expression[0]->checkToken(T_DEFAULT)) {
            $this->condition = new block();
            if (isset($expression[1])) {
                $this->block     = $expression[1];
            } else {
                $this->block     = new block();
            }
        } else {
            $this->stopOnError("Unexpected TOKEN received : '".$expression[0]->getToken()."' in ".__METHOD__);
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getCondition() {
        if ($this->condition == 'default') {
            return null;
        } else {
            return $this->condition;
        }
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        if ($this->condition != 'default') {
            $this->condition->detach();
        }
        $this->block->detach();
    }

    function getRegex(){
        return array('case_block_regex',
                     'case_between_regex',
                    );
    }

}

?>