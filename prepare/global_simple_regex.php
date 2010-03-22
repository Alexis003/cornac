<?php

class global_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_GLOBAL);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }

        if ($t->checkToken(array(T_GLOBAL)) &&
            $t->getNext()->checkClass('variable')  
            ) {

                $var = $t->getNext(1);

                while($var->checkCode(',')) {
                    if ($var->getNext()->checkClass('variable')) {
                        
                            $args = array(0);
                            $remove = array(1);
                            
                            $repl = $var->getNext();
                            $var = $var->getNext(1);
                            
                            $regex = new modele_regex('_global',$args, $remove);
                            Token::applyRegex($repl, '_global', $regex);
                            continue;
                        }
                        die(__CLASS__."else!!\n");
                }
                
                if ( $var->checkCode(';')) {
                    $this->args   = array(1);
                    $this->remove = array(1, 2);

                    mon_log(get_class($t)." => ".__CLASS__);
                    return true; 
                }
                
                $this->args = array(1);
                $this->remove = array(1);

                mon_log(get_class($t)." => ".__CLASS__);
                return true;
        } else {
            return false;
        }
    }
}

?>