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

class variable extends token {
    protected $name = null;

    function __construct($expression = null) {
        parent::__construct(array());

        if (is_null($expression)) { // @note  coming from class tableau
            return ;
        }

        if (count($expression) == 1) {
            if ($expression[0]->checkClass(array('variable','Token'))) {
                $this->name = $expression[0]->getCode();
            } else {
                $this->name = $expression[0];
            }
            $this->setLine($expression[0]->getLine());
        } else {
          $this->name = $expression[1];
          $this->code = $this->name->getCode();
          $this->setLine($this->name->getLine());
        }
    }

    function __toString() {
        return __CLASS__." ".$this->name;
    }
    
    function getName() {
        return $this->name;
    }
    
    function neutralise() {
        if (is_object($this->name)) {
            $this->name->detach();
        }
    }

    function getRegex(){
        return array('variable_regex',
                     'variable_accolade_regex',
                     'variable_accoladeseparee_regex',
                     'variable_variable_regex',
                     );
    }
}

?>