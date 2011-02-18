<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

class analyseur {
    public $verifs = 0;
    public $rates = array();
    private $structures = array();
    private $any_token = false; 

    function __construct($restrict = array()) {
        // @todo this structure order has an impact on function and speed. This may be interesting to study
        $this->structures = array(
                                  'ifthen',
                                  'literals', 
                                  'Cornac_Tokenizeur_Token_Variable', //'variable', 
                                  '_array', 
                                  'rawtext', 
                                  'parenthesis', 
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
                                  'ternaryop',
                                  'block',
                                  'not',
                                  'invert',
                                  '_function', // @attention : function must be before logical
                                  'logical',
                                  'preplusplus',
                                  'postplusplus',
                                  'property',
                                  'property_static',
                                  'Cornac_Tokenizeur_Token_Comparison', //'comparison',
                                  'method',
                                  'method_static',
                                  'Cornac_Tokenizeur_Token_New', // @removing '_new',
                                  '_foreach',
                                  'Cornac_Tokenizeur_Token_While', //'_while',
                                  '_dowhile',
                                  'Cornac_Tokenizeur_Token_Switch',//'_switch',
                                  '_case',
                                  '_default',
                                  'keyvalue',
                                  '_break',
                                  '_continue',
                                  'opappend',
                                  '_constant',
                                  'constant_static',
                                  'constant_class',
                                  '_global',
                                  'Cornac_Tokenizeur_Token_Return', //'_return',
                                  'typehint',
                                  '_class',
                                  'Cornac_Tokenizeur_Token_Interface', //'_interface',
                                  'Cornac_Tokenizeur_Token_Var', //'_var',
                                  'reference',
                                  'sign',
                                  'cast',
                                  'Cornac_Tokenizeur_Token_Static', //'_static',
                                  'Cornac_Tokenizeur_Token_Try',//'_try',
                                  '_catch',
                                  'Cornac_Tokenizeur_Token_Bitshift', //'bitshift',
                                  'Cornac_Tokenizeur_Token_Throw', //'_throw',
                                  '_clone',
                                  '_declare',
                                  'Cornac_Tokenizeur_Token_Shell', //'shell',
                                  '___halt_compiler',
                                  '_closure',
                                  'Cornac_Tokenizeur_Token_Goto', //'_goto',
                                  'Cornac_Tokenizeur_Token_Label', //'label',
                                  'Cornac_Tokenizeur_Token_Nsname', //'_nsname',
                                  'Cornac_Tokenizeur_Token_Namespace', //'_namespace',
                                  'Cornac_Tokenizeur_Token_Use', //'_use',
                                  );
        $this->regex = array();
        foreach ($this->structures as $id => $structure) {
            if (!method_exists($structure, 'getRegex')) { continue; }
            $regex = $structure::getRegex(); 
            
            foreach($regex as $r) {
                $object = new $r; 
                $tokens = $object->getTokens();
                
                if ($tokens === false) { 
                    $this->regex[0][$r] = $object;
                    print "$r doesn\'t have getToken()\n"; 
                } elseif (count($tokens) > 0) {
                    foreach($tokens as $token) {
                        if (in_array($token, $restrict) || $token === 0 || $token > 450) {
                            $this->regex[$token][$r] = $object;
                        }
                    }
                } else {
                    $this->regex[0][$r] = $object;
                }
                
                $this->tokens[$r] = $structure;
            }
        }
    }
    
    private function analog($message, $duration, $success) {
//        return false;
        // @todo move this to Cornac_log class! 
        if (!isset($this->fp)) {
            $this->fp = fopen("/tmp/analyseur.log","a");
        }
        $duration *= 1000000;
        $this->order++;
        fwrite($this->fp, "$this->order\t$message\t$duration\t".getmypid()."\t$success\n");
    }

    public function setAny_Token($flag) {
        $this->any_token = true;
    }

    public function upgrade(Cornac_Tokenizeur_Token $t) {
        $token = $t->getToken();
        
        // @note we won't process those one. Just skip it. 
        if ($t->checkOperator(array(']','}',')',':',';'))) { return $t; }
        if ($t->checkToken(array(T_ENDDECLARE, 
                                 T_ENDFOR,
                                 T_ENDFOREACH, 
                                 T_ENDIF, 
                                 T_ENDSWITCH, 
                                 T_ENDWHILE, 
                                 T_END_HEREDOC))) { return $t; }

        if ($token > 0 && isset($this->regex[$token])) {
            foreach($this->regex[$token] as $name => $regex) {
                $this->verifs++;
                
                $debut = microtime(true);
                if (!$regex->check($t)) {
                    $fin = microtime(true);
                    $this->analog($name, $fin - $debut, 0);
                    $this->rates[] = $name;
                    continue;
                }
                $fin = microtime(true);
                $this->analog($name, $fin - $debut, 1);
    
                $return = analyseur::applyRegex($t, $this->tokens[$name], $regex);
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".get_class($return));
                return $return; 
            }
        } // @empty_else
        
        $code = $t->getCode();
        if (isset($this->regex[$code])) {
            foreach($this->regex[$code] as $name => $regex) {
                $this->verifs++;

                $debut = microtime(true);
                if (!$regex->check($t)) {
                    $fin = microtime(true);
                    $this->analog($name, $fin - $debut, 0);
                    $this->rates[] = $name;
                    continue;
                }
                $fin = microtime(true);
                $this->analog($name, $fin - $debut, 1);
                
                $return = analyseur::applyRegex($t, $this->tokens[$name], $regex);
                if ($return->getLine() == -1) { 
                    print $t->getLine()."\n"; 
                    print $return."\n"; 
                    die(__METHOD__."\n"); 
                }
                Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".get_class($return));
                return $return; 
            }
        }   // @empty_else
        
        if (!$this->any_token) { return $t; }
        
        foreach($this->regex[0] as $name => $regex) {
            $debut = microtime(true);
            if (!$regex->check($t)) {
                $fin = microtime(true);
                $this->analog($name, $fin - $debut, 0);
                continue;
            }
            $fin = microtime(true);
            $this->analog($name, $fin - $debut, 1);

            $return = analyseur::applyRegex($t, $this->tokens[$name], $regex);
            Cornac_Log::getInstance('tokenizer')->log(get_class($t)." => ".get_class($return));
            return $return; 
        }
        return $t;
    }
    
    function applyRegex($token, $class, $regex) { 
        $args = $regex->getArgs();
        
        $argNext = 0;
        $tNext = $token;
        foreach($args as $id => $arg) {
            if ($arg > 0) {
                $args[$id] = $tNext->getNext($arg - $argNext - 1);
                $argNext = $arg;
                $tNext = $args[$id];
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
            }   // @empty_else
        }

        $return->replace($token);
        $return->setToken(0);
        $return->neutralise();
        
        $regex->reset();
        
        return $return;
    }
}

?>