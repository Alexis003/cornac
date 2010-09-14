<?php

class block extends instruction {
    protected $list = array();
    
    function __construct($expression = array()) {
        parent::__construct(array());
        
        if (is_null($expression)) {
            $expression = array();
        }

        foreach($expression as $l) {
            if (get_class($l) == 'sequence') {
                $this->list = array_merge($this->list, $l->getElements());
            } elseif (get_class($l) == 'block') {
                $this->list = array_merge($this->list, $l->getList());
            } else {
                $this->list[] = $l;
            }
         }
         if (isset($this->list[0])) {
             $this->setLine($this->list[0]->getLine());
         }
    }

    function __toString() {
        return __CLASS__." {".join("\n", $this->list)." }";
    }

    function getList() {
        return $this->list;
    }

    function getToken() {
        return 0;
    }

    function getCode() {
        return '';
    }

    function neutralise() {
        foreach($this->list as $e) {
            $e->detach();
        }
    }

    function getRegex(){
        return array('block_normal_regex',
                     'block_casedefault_regex',
                     'block_opening_regex',
                    );
    }
}

?>