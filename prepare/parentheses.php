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

class parentheses extends instruction {
    protected $contenu = null;
    
    function __construct($parentheses) {
        parent::__construct(array());
        
        $this->contenu = $parentheses[0];
        $this->setLine($this->contenu->getLine());
    }

    function __toString() {
        return __CLASS__." (".$this->contenu.")";
    }

    function getContenu() {
        return $this->contenu;
    }

    function neutralise() {
        $this->contenu->detach();
    }

    function getRegex(){
        return array('parentheses_normal_regex',
                     );
    }

    function getCode() { return '';}
}

?>