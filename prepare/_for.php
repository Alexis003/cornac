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

class _for extends instruction {
    protected $init = null;
    protected $end = null;
    protected $increment = null;
    protected $block = null;

    function __construct($expression) {
        parent::__construct(array());

        if ($expression[0]->getCode() == ';') {
            $this->init = null;
        } else {
            $this->init = $expression[0];
        }

        // @note immediate processing of block
        $this->block = array_pop($expression);

        if ($expression[1]->getCode() == ';') {
            $this->end = null;
        } elseif ($expression[1]->checkClass('sequence') == 'sequence') {
            $elements = $expression[1]->getElements();
            $this->end = $elements[0];
            // @note what if we get several elements?
        } else {
            $this->end = $expression[1];
        }
        
        if ($expression[2]->getCode() == ')') {
            $this->increment = null;
        } else {
            $this->increment = $expression[2];
        }
        
    }
    
    function __toString() {
        return __CLASS__." for (".$this->init."; ".$this->end."; ".$this->increment." ) {".$this->block."} ";
    }

    function getInit() {
        return $this->init;
    }

    function getEnd() {
        return $this->end;
    }

    function getIncrement() {
        return $this->increment;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        if (!is_null($this->init)) {
            $this->init->detach();
        }
        if (!is_null($this->end)) {
            $this->end->detach();
        }
        if (!is_null($this->increment)) {
            $this->increment->detach();
        }
        $this->block->detach();
    }

    function getRegex() {
        return array(
    'for_simple_regex',
    'for_sequence_regex',
    'for_comma1_regex',
    'for_comma2_regex',
    'for_comma3_regex',
    'for_alternative_regex',
);
    }
}

?>