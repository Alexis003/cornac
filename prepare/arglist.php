<?php

class arglist extends token {
    protected $list = array();
    
    function __construct($list = array()) {
        parent::__construct(array());
        
        foreach($list as $l) {  
            if (is_null($l)) {
                $this->list[] = $l;
            } elseif ($l->checkCode(',')) {
                $this->list[] = null;
            } else {
                $this->list[] = $l;
            }
        }
    }

    function __toString() {
        $return = __CLASS__."(";
        
        if (count($this->list) > 0) {
            foreach($this->list as $a) {
                $return .= $a.", ";
            }
            $return = substr($return, 0, -2).")";
        } else {
            $return = "( )";
        }
        return $return;
    }

    function getList() {
        return $this->list;
    }

    function neutralise() {
        if (!is_array($this->list)) { return null; }

        foreach($this->list as $id => &$a) {
            if (!is_null($a)) {
                $a->detach();
            }
        }
    }
    
   function getRegex() {
        return array(
    'arglist_regex',
                    );
    }
}

?>