<?php

class affectation_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext(1)) { return false; }

        if (!$t->checkForAssignation()) { return false; }
        
        if ( $t->getNext(1)->checkNotCode(array(';','}',')',',',':',']')) &&
             $t->getNext(1)->checkNotClass(array('sequence','block','_foreach','_for','rawtext')) &&
             $t->getNext(1)->checkNotToken(array(T_AS,T_CLOSE_TAG))
                ) { return false;}
                
             
        if ($t->hasPrev(1) && $t->getPrev(1)->checkCode(array('&','$','::','@','->','var','public','private','protected'))) { return false;}
        if (($t->getPrev()->checkClass(array('variable','property','opappend','functioncall','not','noscream','property_static','reference')) || 
             $t->getPrev()->checkSubclass('variable')) &&
            (/*$t->getNext()->checkSubclass(array('instruction'))  || */
             $t->getNext()->checkClass(array('literals', 'variable','tableau','signe','noscream',
                                             'property', 'method'  ,'cdtternaire',
                                             'functioncall','operation','logique',
                                             'method_static','operation','cdtternaire',
                                             'constante_static','property_static','_clone',
                                             'parentheses','_new','cast','constante','invert',
                                             'not','affectation','shell','decalage','comparaison',
                                             'reference','concatenation','variable',
                                             'property_static','postplusplus','preplusplus','inclusion')))
            
            ) {
                $this->args = array(-1, 0, 1);
                $this->remove = array( -1, 1);
                
                mon_log(get_class($t)." => ".__CLASS__);
                return true;
            } else {
                return false;
            }
    }
}

?>