<?php

class analyseur {
    public $verifs = 0;
    public $rates = array();

    function __construct() {
        $this->structures = array(
                                  'ifthen',
                                  'literals', 
                                  'variable', 
                                  'tableau', 
                                  'rawtext', 
                                  'parentheses', 
                                  'affectation', 
                                  '_for',
                                  'sequence', 
                                  'operation', 
                                  'functioncall', 
                                  'noscream', 
                                  'arglist', 
                                  'inclusion', 
                                  'codephp',
                                  'concatenation',
                                  'cdtternaire',
                                  'block',
                                  'not',
                                  'invert',
                                  '_function', // @attention : function must be before logique
                                  'logique',
                                  'preplusplus',
                                  'postplusplus',
                                  'property',
                                  'property_static',
                                  'comparaison',
                                  'method',
                                  'method_static',
                                  '_new',
                                  '_foreach',
                                  '_while',
                                  '_dowhile',
                                  '_switch',
                                  '_case',
                                  '_default',
                                  'clevaleur',
                                  '_break',
                                  '_continue',
                                  'opappend',
                                  'constante',
                                  'constante_static',
                                  'constante_classe',
                                  '_global',
                                  '_return',
                                  'arginit',
                                  'typehint',
                                  '_class',
                                  '_interface',
                                  '_var',
                                  'reference',
                                  'signe',
                                  'cast',
                                  '_static',
                                  '_try',
                                  '_catch',
                                  'decalage',
                                  '_throw',
                                  '_clone',
                                  '_declare',
                                  'shell',
                                  '___halt_compiler',
                                  );
        $this->regex = array();
        foreach ($this->structures as $id => $structure) {
            if (!method_exists($structure, 'getRegex')) { continue; }
            $regex = $structure::getRegex(); 
            
            foreach($regex as $r) {
                $objet = new $r;
                $tokens = $objet->getTokens();
                
                if ($tokens === false) { 
                    $this->regex[0][$r] = $objet;
                    print "$r manque de getToken()\n"; 
                } elseif (count($tokens) > 0) {
                    foreach($tokens as $token) {
                        $this->regex[$token][$r] = $objet;
                    }
                } else {
                    $this->regex[0][$r] = $objet;
                }
                
                
                $this->tokens[$r] = $structure;
            }
        }
    }

    public function upgrade(Token $t) {
        $token = $t->getToken();
        
        if ($token > 0 && isset($this->regex[$token])) {
            foreach($this->regex[$token] as $nom => $regex) {
                $this->verifs++;
                
                if (!$regex->check($t)) {
                    $this->rates[] = $nom;
                    unset($regex);
                    continue;
                }
    
                $return = analyseur::applyRegex($t, $this->tokens[$nom], $regex);
                mon_log(get_class($t)." => ".get_class($return));
                return $return; 
            }
        } else {
            // @empty_ifthen
        }
        
        $code = $t->getCode();
        if (isset($this->regex[$code])) {
            foreach($this->regex[$code] as $nom => $regex) {
                $this->verifs++;
                
                if (!$regex->check($t)) {
                    $this->rates[] = $nom;
                    unset($regex);
                    continue;
                }
    
                $return = analyseur::applyRegex($t, $this->tokens[$nom], $regex);
                if ($return->getLine() == -1) { 
                    print $t->getLine()."\n"; 
                    print $return."\n"; 
                    die(__METHOD__."\n"); 
                }
                mon_log(get_class($t)." => ".get_class($return));
                return $return; 
            }
        } else {
            // @empty_ifthen
        }
        
        foreach($this->regex[0] as $nom => $regex) {
            if (!$regex->check($t)) {
                unset($regex);
                continue;
            }

            $return = analyseur::applyRegex($t, $this->tokens[$nom], $regex);
            mon_log(get_class($t)." => ".get_class($return));
            return $return; 
        }
        return $t;
    }
    
    function applyRegex($token, $class, $regex) { 
        $args = $regex->getArgs();
        
        foreach($args as $id => $arg) {
            if ($arg > 0) {
                $args[$id] = $token->getNext($arg - 1);
            } elseif ($arg < 0) {
                $args[$id] = $token->getPrev(abs($arg + 1));
            } else {        
                $args[$id] = $token;
            }
        }
        
        $return = new $class($args);
        $return->copyToken($token);

        $remove = $regex->getRemove();
        foreach($remove as $arg) {
            if ($arg > 0) {
                $token->removeNext($arg - 1);
            } elseif ($arg < 0) {
                $token->removePrev($arg + 1);
            } else {
                // @empty_ifthen Just ignore this
            }
        }

        $return->replace($token);
        $return->setToken(0);
        $return->neutralise();
        
        $regex->reset();
        
        return $return;
    }
}

?>