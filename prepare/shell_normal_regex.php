<?php

class shell_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
        
        $this->sequence_classes = array('literals',
                                                  'variable',
                                                  'tableau',
                                                  'property',
                                                  'property_static',
                                                  'method',
                                                  'method_static',
                                                  'constante_static',
                                                  'sequence',
                                                  );
    }

    function getTokens() {
        return array('`');
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotOperateur('`')) { return false; } 
        
        if ($t->checkOperateur('`') ) {
            $token_fin = '`';
        } else {
            die($t."\n"."Can't reach here ".__METHOD__);
        }

        $var = $t->getNext(); 
        $this->args   = array(  );
        $this->remove = array(  );
        $pos = 1;
        
        while ($var->checkNotCode($token_fin)) {
            if ($var->checkCode('{') && 
                $var->getNext()->checkClass($this->sequence_classes) && 
                  $var->getNext(1)->checkCode('}')) {

                $regex = new modele_regex('variable',array(0), array(-1, 1));
                Token::applyRegex($var->getNext(), 'variable', $regex);

                mon_log(get_class($var->getNext())." => ".get_class($var->getNext())." (".__CLASS__.")");
                return false;
            }

            if ($var->checkNotClass($this->sequence_classes) &&
                $var->checkNotClass(array('signe'))) { return false; }
        
            $this->args[]    = $pos;
            $this->remove[]  = $pos;

            $pos += 1;
            $var = $var->getNext();
        }

        $this->remove[]  = $pos; // " final
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>