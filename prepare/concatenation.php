<?php

class concatenation extends instruction {
    protected $list = array();
    
    function __construct($list) {
        parent::__construct(array());
        
        foreach($list as $l) {
            if (get_class($l) == 'concatenation') {
                $this->list = array_merge($this->list, $l->getList());
            } elseif (get_class($l) == 'sequence') {
                $this->list = array_merge($this->list, $l->getElements());
            } elseif (get_class($l) == 'block') {
                $this->list = array_merge($this->list, $l->getList());
            } else {
                $this->list[] = $l;
            }
         }
    }

    function __toString() {
        $return = __CLASS__." ";
        
        foreach($this->list as $a) {
            $return .= $a." . ";
        }
        $return = substr($return, 0, -2)." ";
        return $return;
    }

    function getList() {
        return $this->list;
    }

    function neutralise() {
        foreach($this->list as $id => $a) {
            $a->detach();
        }
    }

    private function mange($concatenation) {
        $this->list = array_merge($this->list, $concatenation->getList());
        $this->neutralise();
    }

   function getRegex() {
        return array(
        'concatenation_regex',
        'concatenation_interpole_regex',
                    );
    }    
}

?>