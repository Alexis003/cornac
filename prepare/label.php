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

class label extends instruction {
    protected $name = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        $this->name = $this->makeProcessedToken('_label_', $expression[0]);
    }

    function __toString() {
        $return = $this->name." : ";
        return $return;
    }

    function getName() {
        return $this->name;
    }

    function neutralise() {
        $this->name->detach();
    }

    function getRegex(){
        return array('label_normal_regex',
                    );
    }

}

?>