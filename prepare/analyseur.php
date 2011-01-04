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

    function __construct() {
        $this->structures = array(
                                  'ifthen',
                                  'literals', 
                                  'variable', 
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
                                  'comparison',
                                  'method',
                                  'method_static',
                                  '_new',
                                  '_foreach',
                                  '_while',
                                  '_dowhile',
                                  '_switch',
                                  '_case',
                                  '_default',
                                  'keyvalue',
                                  '_break',
                                  '_continue',
                                  'opappend',
                                  'constante',
                                  'constante_static',
                                  'constante_class',
                                  '_global',
                                  '_return',
                                  'arginit',
                                  'typehint',
                                  '_class',
                                  '_interface',
                                  '_var',
                                  'reference',
                                  'sign',
                                  'cast',
                                  '_static',
                                  '_try',
                                  '_catch',
                                  'bitshift',
                                  '_throw',
                                  '_clone',
                                  '_declare',
                                  'shell',
                                  '___halt_compiler',
                                  '_closure',
                                  '_goto',
                                  'label',
                                  '_nsname',
                                  '_namespace',
                                  '_use',
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
                        $this->regex[$token][$r] = $object;
                    }
                } else {
                    $this->regex[0][$r] = $object;
                }
                
                
                $this->tokens[$r] = $structure;
            }
        }
        /*
        foreach($this->regex as $key => $value) {
            print $key."\n";
            print "  ".join("\n  ", array_keys($value))."\n";
        }
        die();*/
    }
    
    private function analog($mess) {
//        return false;
        if (!isset($this->fp)) {
            $this->fp = fopen("/tmp/analyseur.log","a");
        }
        fwrite($this->fp, "$mess\t".getmypid()."\t\n");
    }

    public function upgrade(Token $t) {
        $token = $t->getToken();
        
        // @note we won't process those one. Just skip it. 
        if ($t->checkOperator(array(']','}',')',':',';',','))) { return $t; }

        if ($token > 0 && isset($this->regex[$token])) {
            foreach($this->regex[$token] as $name => $regex) {
                $this->verifs++;
                
                $this->analog($name);
                if (!$regex->check($t)) {
                    $this->rates[] = $name;
                    unset($regex);
                    continue;
                }
    
                $return = analyseur::applyRegex($t, $this->tokens[$name], $regex);
                mon_log(get_class($t)." => ".get_class($return));
                return $return; 
            }
//            return $t;
        } // @empty_else
        
        $code = $t->getCode();
        if (isset($this->regex[$code])) {
            foreach($this->regex[$code] as $name => $regex) {
                $this->verifs++;
                
                $this->analog($name);
                if (!$regex->check($t)) {
                    $this->rates[] = $name;
                    unset($regex);
                    continue;
                }
    
                $return = analyseur::applyRegex($t, $this->tokens[$name], $regex);
                if ($return->getLine() == -1) { 
                    print $t->getLine()."\n"; 
                    print $return."\n"; 
                    die(__METHOD__."\n"); 
                }
                mon_log(get_class($t)." => ".get_class($return));
                return $return; 
            }
//            return $t;
        }   // @empty_else
        
        foreach($this->regex[0] as $name => $regex) {
            $this->analog($name);
            if (!$regex->check($t)) {
                unset($regex);
                continue;
            }

            $return = analyseur::applyRegex($t, $this->tokens[$name], $regex);
            mon_log(get_class($t)." => ".get_class($return));
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