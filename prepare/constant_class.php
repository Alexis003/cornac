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

class constant_class extends token {
    protected $name = null;
    protected $constant = null;
    
    function __construct($expression) {
        parent::__construct();
        
        $this->name = $expression[0];
        $this->constant = $expression[1];
    }

    function getName() {  
        return $this->name;
    }

    function getConstant() {
        return $this->constant;
    }

    function neutralise() {
        $this->name->detach();
        $this->constant->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->name."::".$this->constant;
    }

    function getRegex(){
        return array('constant_class_regex',
                     );
    }

}

?>