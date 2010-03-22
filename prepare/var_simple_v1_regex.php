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
            $t->getNext()->checkClass(array('variable','affectation'))
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
                            $i++;
                        
                            // 1 : -3
                            // 2 : -5
                            if ($this->args[0] == -1) {
                                $args = array(-3 - $i ,-2 - $i , 0);
                            } else {
                                $args = array(-2 - $i , 0);
                            }
                            $remove = array(-1);
                            
                            $repl = $var->getNext();
                            $var = $var->getNext(1);
                            
                            $regex = new modele_regex('_var',$args, $remove);
                            Token::applyRegex($repl, '_var', $regex);
                            continue;
                        }

                        if ($var->getNext()->checkClass(array('_var'))) {
                            // tout va bien, c'est déjà fait. 
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

                        $this->args[] = 3;
                        $this->remove[] = 2;
                        $this->remove[] = 3;

                        mon_log(get_class($t)." (".__CLASS__.")  => Affectation");
                        return true;
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