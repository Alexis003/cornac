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

class _return extends instruction {
    protected $return = null;

    function __construct($expression = null) {
        parent::__construct(array());

        if (isset($expression[0])) {
            $this->return = $expression[0];
        } 
    }
    
    function __toString() {
        return __CLASS__." return ".$this->return;
    }

    function getReturn() {
        return $this->return;
    }

    function neutralise() {
        if (!is_null($this->return)) {
            $this->return->detach();
        }
    }

    function getRegex() {
        return array(
        'return_simple_regex',
        'return_empty_regex',
);
    }
}

?>