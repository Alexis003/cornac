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

class literals extends token {
    private $value = null;     // value of the literal
    private $delimiter = null; // delimter used. Used for string literals
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        $this->value = $expression[0]->getCode();
        if ($this->value[0] == '"' || $this->value[0] == "'") {
            $this->delimiter = $this->value[0];
            $this->value = trim($this->value, "'\"");
        }

        if (isset($signe[1])) {
            if ($signe[1]->checkCode("-")){
                $this->value = -1 * $this->value;
            }
        }
    }
    
    function getCode() {
        if (strlen($this->value) && ($this->value[0] == '"' || $this->value[0] == "'")) {
            $this->delimiter = $this->value[0];
            $this->value = trim($this->value, "'\"");
        }
        return $this->value;
    }

    function __toString() {
        return __CLASS__." ".$this->value;
    }

    function getLiteral() {
        return $this->value;
    }

    function getDelimiter() {
        return $this->delimiter;
    }

    static function getRegex() {
        return array('literals_regex',
                     'literals_heredoc_regex',
                    );
    }
}

?>