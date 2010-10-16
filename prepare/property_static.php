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

class property_static extends token {
    protected $class = null;
    protected $property = null;
    
    function __construct($expression) {
        parent::__construct();
        
        if (is_array($expression)) {
            $this->class = $this->makeToken_traite($expression[0]);
            $this->property = $expression[1];
        } else {
            $this->stopOnError("Wrong number of arguments  : '".count($expression)."' in ".__METHOD__);
        }
    }

    function getClass() {  
        return $this->class;
    }

    function getProperty() {
        return $this->property;
    }

    function neutralise() {
        $this->class->detach();
        $this->property->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->class."::".$this->property;
    }

    function getRegex(){
        return array('property_static_regex',
                     );
    }

}

?>