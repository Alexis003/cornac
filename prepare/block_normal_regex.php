<?php

class block_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('{');
    }
    
    function check($t) {
        if ($t->checkNotCode('{') )   { return false; }
        if ($t->hasPrev() && $t->getPrev()->checkCode(array('->',']')))   { return false; }
        if ($t->hasPrev() && $t->getPrev()->checkClass(array('property','variable','property_static','tableau')))  { return false; }
        if ($t->checkClass('block') ) { return false; }
        if (!$t->hasNext())           { return false; }

        $this->remove[] = 0;
        
        $var = $t->getNext();            
        $i = 1;

        while($var->checkNotCode('}')) {
            if ($var->checkForBlock(true)) {
                $this->args[] = $i;
                $this->remove[] = $i;
                if (!$var->hasNext()) { return $t; }
                $var = $var->getNext();
                $i++;
                continue;
            }

            if ($var->checkNotClass(array('block','Token')) && 
//                !$var->hasNext(1) && 
                $var->getNext()->checkCode(';')) {
                $this->args[] = $i;

                $this->remove[] = $i;
                $this->remove[] = $i + 1;
                if (!$var->hasNext(1)) { return $t; }
                $var = $var->getNext(1);
                $i += 2;
                continue;
            }

            if ($var->checkCode('{') ) {
                // bloc imbriqués ? Alors, on annule tout.
                $this->args = array();
                $this->remove = array();
                return false;
            }

            if ($var->checkCode(';') ) {
                // un point-virgule qui traine. Bah....
                $this->remove[] = $i;
                $i++;
                if (!$var->hasNext()) { return $t; }
                $var = $var->getNext();
                continue;
            }

            // pas traitable ? On annule tout.
            $this->args = array();
            $this->remove = array();
            return false;
        }
        
        $this->remove[] = $i ; // } final
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true;
    }
}
?>