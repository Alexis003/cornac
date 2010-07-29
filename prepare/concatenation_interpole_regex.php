<?php

class concatenation_interpole_regex extends analyseur_regex {
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
        return array(T_START_HEREDOC, '"');
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotCode('"')  && $t->checkNotToken(T_START_HEREDOC)) { return false; } 
        if ($t->checkClass('concatenation') ) { return false; } 
        
        if ($t->checkOperateur('"') ) {
            $token_fin = '"';
        } elseif ($t->checkToken(T_START_HEREDOC) ) {
            $token_fin = trim(substr($t->getCode(), 3));
        } elseif ($t->checkClass('rawtext') ) {
            return false; 
        } else {
            $this->stop_on_error($t."\n"."Can't reach here ".__METHOD__);
        }

        $var = $t->getNext(); 
        $this->args   = array(  );
        $this->remove = array(  );
        $pos = 1;
        
        while ($var->checkNotCode($token_fin)) {
            if ($var->checkCode('{') && 
                $var->getNext()->checkClass($this->sequence_classes) && 
                $var->getNext(1)->checkCode('}')) {

                if ($var->getNext()->checkClass(array('tableau','property'))) {
                    $this->args[] = $pos + 1;
                    
                    $this->remove[] = $pos; 
                    $this->remove[] = $pos + 1; 
                    $this->remove[] = $pos + 2; 
                    
                    $pos += 3;
                    $var = $var->getNext(2);
                    continue;
                } else {
                    $regex = new modele_regex('variable',array(0), array(-1, 1));
                    Token::applyRegex($var->getNext(), 'variable', $regex);

                    mon_log(get_class($var->getNext())." => ".get_class($var->getNext())." (".__CLASS__.")");
                    return false;
                }
            }

            if ($var->checkNotClass($this->sequence_classes) &&
                $var->checkNotClass(array('signe','block'))) { return false; }
        
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