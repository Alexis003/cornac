<?php

class cast_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_INT_CAST, 
                                 T_DOUBLE_CAST, 
                                 T_STRING_CAST,
                                 T_ARRAY_CAST,
                                 T_BOOL_CAST,
                                 T_OBJECT_CAST,
                                 T_UNSET_CAST);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(array(T_INT_CAST, 
                                 T_DOUBLE_CAST, 
                                 T_STRING_CAST,
                                 T_ARRAY_CAST,
                                 T_BOOL_CAST,
                                 T_OBJECT_CAST,
                                 T_UNSET_CAST)) &&
            $t->getNext()->checkNotClass('Token') && 
            ($t->getNext(1)->checkNotCode(array('[','->','{')))
            ) {

            $this->args = array(0, 1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>