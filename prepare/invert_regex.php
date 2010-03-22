<?php

class invert_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('~');
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkNotClass('Token')) { return false; }
        if ($t->getNext()->checkClass(array('functioncall','variable','tableau',
                                            'method','property','_new','comparaison',
                                            'parentheses','constante','literals',
                                            'constante_static','property_static','method_static',
                                            'cast','invert','noscream','signe')) &&
            $t->getNext(1)->checkNotCode(array('->','[','{','::'))
            ) {

            $this->args = array(1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>