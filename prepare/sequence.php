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

class sequence extends instruction {
    protected $elements = array();
    
    function __construct($elements) {
        parent::__construct(array());
        
        foreach($elements as $l) {
            if (get_class($l) == 'sequence') {
                $this->elements = array_merge($this->elements, $l->getElements());
            } elseif (get_class($l) == 'block') {
                $this->elements = array_merge($this->elements, $l->getList());
            } elseif (get_class($l) == 'codephp') {
                $code = $l->getphp_code();
                if (get_class($code) == 'sequence') {
                    $this->elements = array_merge($this->elements, $code->getElements());
                } else {
                    $this->elements[] = $code;
                }
            } else {
                $this->elements[] = $l;
            }
         }
    }

    function __toString() {
        $return = __CLASS__;
        if (count($this->elements) == 0) {
            $return .= "Sequence vide\n";
        } else {
            foreach($this->elements as $e) {
                $return .= $e."\n";
            }
        }
        return $return;
    }

    function getCode() {
        return __CLASS__;
    }
    
    function getElements() {
        return $this->elements;
    }

    function neutralise() {
        foreach($this->elements as $e) {
            $e->detach();
        }
    }

    private function mange($sequence) {
        $this->elements = array_merge($this->elements, $sequence->getElements());
        $this->neutralise();
    }
    
    public function getRegex() {
        return array(
          'sequence_regex',
          'sequence_suite_regex',
          'sequence_class_regex',
          'sequence_empty_regex',
          'sequence_cdr_regex',
                    );
    }    
}

?>