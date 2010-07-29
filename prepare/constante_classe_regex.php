<?php

class constante_classe_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CONST);
    }
    
    function check($t) {
        if (!$t->hasNext(3) ) { return false; }

        if ( $t->checkToken(T_CONST) && 
            $t->getNext()->checkClass('constante') &&
            $t->getNext(1)->checkCode('=') &&
            $t->getNext(2)->checkNotClass('Token')
            ) {
                $var = $t->getNext(3);
                
                while($var->checkCode(',')) {
                    if ($var->getNext()->checkClass('constante') &&
                        $var->getNext(1)->checkCode('=') &&
                        $var->getNext(2)->checkNotClass('Token')) {
                        
                            $args = array(0,2);
                            $remove = array(1,2,3);
                            
                            $repl = $var->getNext();
                            $var = $var->getNext(3);
                            
                            $regex = new modele_regex('constante_classe',$args, $remove);
                            Token::applyRegex($repl, 'constante_classe', $regex);
                            continue;
                        }
                    // @note if we reach here, there is a problem
                    return false;
                }
                
                if ( $var->checkCode(';')) {
                    $this->args   = array(1, 3);
                    $this->remove = array(1, 2, 3, 4);

                    mon_log(get_class($t)." => ".__CLASS__);
                    return true; 
                }
        } 
        return false;
    }
}
?>