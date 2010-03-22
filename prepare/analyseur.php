<?php

class analyseur {
    public $verifs = 0;
    public $rates = array();

    function __construct() {

        $this->structures = array(//'whitespace', 
//                                  'commentaire',
                                  'ifthen',
                                  'literals', 
                                  'variable', 
                                  'tableau', 
                                  'rawtext', 
                                  'parentheses', 
                                  'affectation', 
//                                  'instruction', 
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
                                  '_function', // function doit passer avant logique
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
        foreach ($this->structures as $id => $s) {
            if (!method_exists($s, 'getRegex')) { continue; }
            $regex = $s::getRegex(); 
            
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
                
                
                $this->tokens[$r] = $s;
            }
        }
    }

    function upgrade($t ) {
        return $this->factory($t);
        
            $retour = $this->factory($t);
            if (is_null($retour)) {
                print "$s a retourne null pour $this\n";
                die();
            }
            if ($t != $retour) {
                return $retour;
            }
        return $retour;
    }

    public function factory(Token $t) {
        $token = $t->getToken();
        
        if ($token > 0 && isset($this->regex[$token])) {
            foreach($this->regex[$token] as $nom => $r) {
                $this->verifs++;
                
                if (!$r->check($t)) {
                    $this->rates[] = $nom;
                    unset($r);
                    continue;
                }
    
                $retour = analyseur::applyRegex($t, $this->tokens[$nom], $r);
                mon_log(get_class($t)." => ".get_class($retour));
                return $retour; 
            }
//            return $t;
        } // else implicite
        
        $code = $t->getCode();
        if (isset($this->regex[$code])) {
//            print "$code detecte\n";
            foreach($this->regex[$code] as $nom => $r) {
                $this->verifs++;
                
                if (!$r->check($t)) {
                    $this->rates[] = $nom;
                    unset($r);
                    continue;
                }
    
                $retour = analyseur::applyRegex($t, $this->tokens[$nom], $r);
                mon_log(get_class($t)." => ".get_class($retour));
                return $retour; 
            }
        } // else implicite

        foreach($this->regex[0] as $nom => $r) {
            if (!$r->check($t)) {
                unset($r);
                continue;
            }

            $retour = analyseur::applyRegex($t, $this->tokens[$nom], $r);
            mon_log(get_class($t)." => ".get_class($retour));
            return $retour; 
        }
        return $t;

    }
    
    function applyRegex($t, $class, $r) { 
        $args = $r->getArgs();
        
        foreach($args as $id => $arg) {
            if ($arg > 0) {
                $args[$id] = $t->getNext($arg - 1);
            } elseif ($arg < 0) {
                $args[$id] = $t->getPrev(abs($arg + 1));
            } else {        
                $args[$id] = $t;
            }
        }
        
        if (empty($args)) {
            $retour = new $class();
        } else {
            $retour = new $class($args);
        }
        $retour->copyToken($t);

        $remove = $r->getRemove();
        foreach($remove as $arg) {
            if ($arg > 0) {
                $t->removeNext($arg - 1);
            } elseif ($arg < 0) {
                $t->removePrev($arg + 1);
            } else {
                // rien, c'est une erreur
            }
        }

        $retour->replace($t);
        $retour->setToken(0);
        $retour->neutralise();
        
        $r->reset();
        
        return $retour;
    }
}

?>