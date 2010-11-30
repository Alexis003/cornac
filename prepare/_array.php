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

class _array extends variable {
    protected $variable = null;
    protected $index = null;

    function __construct($variable) {
        parent::__construct();
        
        if (is_array($variable)) {
            $this->variable = $variable[0];
            $this->index = $variable[1];
        } else {
            $this->stopOnError('No way we end up here : '.__METHOD__);
        }
    }
        
    function neutralise() {
        $this->variable->detach();
        $this->index->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->variable."[".$this->index."]";
    }

    function getVariable() {
        return $this->variable;
    }

    function getIndex() {
        return $this->index;
    }

    function getRegex(){
        return array('array_regex',
                     'array_curly_regex',
                     );
    }
}

?>