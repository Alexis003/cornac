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

class typehint extends token {
    protected $type = null;
    protected $name = null;

    function __construct($expression = null) {
        parent::__construct(array());
        
        if (count($expression) != 2) { 
            $this->stop_on_error("Number of argument is wrong");
        }
        
        $this->type = $this->make_token_traite($expression[0]);
        $this->name = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->type." ".$this->name;
    }
    
    function getName() {
        return $this->name;
    }

    function getType() {
        return $this->type;
    }

    function neutralise() {
        $this->type->detach();
        $this->name->detach();
    }

    function getRegex(){
        return array('typehint_regex');
    }
}

?>