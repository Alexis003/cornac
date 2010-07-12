<?php

class var_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }

        if ($t->checkToken(array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC)) &&
            $t->getNext()->checkClass(array('variable','affectation','arginit'))
            ) {
                $this->args = array(0, 1);
                $this->remove = array(1);

                if ($t->hasPrev() &&
                    $t->getPrev()->checkToken(array(T_VAR, T_PRIVATE, T_PROTECTED, T_PUBLIC, T_STATIC))) {

                    $this->args[] = -1;
                    $this->remove[] = -1;

                    sort($this->args);
                    sort($this->remove);
                }
                
                $var = $t->getNext(1);
                
                if ($var->checkCode('=') &&
                    $var->getNext()->checkClass(array('functioncall','literals','signe'))) {

                        $args = array(0,1, 2);
                        $remove = array(0,1, 2);
                            
                        $regex = new modele_regex('affectation',$args, $remove);
                        Token::applyRegex($t->getNext(), 'affectation', $regex);
                            
                        mon_log(get_class($t)." (".__CLASS__.")  => Affectation");
                        return false;
                }

                $i = 0;
                while($var->checkCode(',')) {
                    if ($var->getNext()->checkClass('variable') && 
                        $var->getNext(1)->checkCode('=') && 
                        $var->getNext(2)->checkClass(array('literals','functioncall','constante','signe'))) {

                            $args = array(0,1, 2);
                            $remove = array(0,1, 2);
                            
                            $regex = new modele_regex('affectation',$args, $remove);
                            Token::applyRegex($var->getNext(), 'affectation', $regex);
                            
                            mon_log(get_class($t)." (".__CLASS__.")  => Affectation");
                            return false;
                    }

                    if ($var->getNext()->checkClass(array('variable','affectation','arginit')) &&
                        $var->getNext(1)->checkNotCode('=')) {
                            $i += 2;
                        
                            $this->args[] = $i + 1;
                            $this->remove[] = $i;
                            $this->remove[] = $i + 1;
                            
                            if (!$var->hasNext(1)) { return false;}
                            $var = $var->getNext(1);

                            continue;
                        }
                        return false;
                }
                
                if ( $var->checkCode(';')) {
                    mon_log(get_class($t)." => ".__CLASS__);
                    return true; 
                } elseif ($var->checkCode('=')) {
                    if ($var->getNext()->checkClass(array('literals','functioncall','constante','constante_static')) &&
                        $var->getNext(1)->checkCode(';')) {

                        $args = array(0,1, 2);
                        $remove = array(0,1, 2);

                        $regex = new modele_regex('affectation',$args, $remove);
                        Token::applyRegex($t->getNext(), 'affectation', $regex);
                            
                        mon_log(get_class($t)." (".__CLASS__.")  => Affectation");
                        return false;
                    }
                } else {
                    return false;
                }
                
                return false;
        } else {
            return false;
        }
    }
}

?>