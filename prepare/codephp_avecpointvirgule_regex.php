<?php

class codephp_avecpointvirgule_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }

    function check($t) {
        if (!$t->hasNext(2)) { return false; }
        
        if ($t->getNext()->checkClass('Token')) { return false; }
        if ($t->getNext(1)->checkNotCode(';')) { return false; }
        if ($t->getNext(2)->checkNotToken(T_CLOSE_TAG)) { return false; }

//        print __METHOD__."\n";
//        if ($t->hasNext(3)) { print "OK";} else { print "KO";}
//        if () { print "KO\n";}
        
        if ($t->hasNext(3) && $t->getNext(3)->checkToken(T_OPEN_TAG)) {
            // cas du empty raw text
            return false;
        }

        if ($t->hasNext(4) && $t->getNext(4)->checkToken(T_OPEN_TAG)) {
            // cas du raw text non vide
            return false;
        }
        
        $this->args = array(1);
        $this->remove = array(1,2,3);
        
        mon_log(get_class($t)." => codePHP (".__CLASS__.")");
        return true;
    }
}

?>