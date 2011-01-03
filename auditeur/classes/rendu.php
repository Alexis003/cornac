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

class rendu {
    function __construct($mid) {
        $this->mid = $mid;
    }
    
    function rendu($left, $right, $file) {
        $this->file = $file;
        $sql_file = $this->mid->quote($file);

        $query = <<<SQL
SELECT * FROM rd
WHERE left >= $left AND 
      right <= $right AND 
      file = $sql_file 
ORDER BY left
SQL;
        $res = $this->mid->query($query);
        
        $this->lignes = array();
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->lignes[$ligne['left']] = $ligne;
            if (!isset($debut)) {
                $debut = $ligne['left'];
            }
        }
        
        if (!isset($debut)) {
            print "$left $right, $file\n$query\n";
            return '';
        }

        $this->traite($debut);
        return $this->lignes[$debut];
    }
    
    function traite($left) {
        if (!isset($this->lignes[$left])) { return ; } // @note already done
        if (is_string($this->lignes[$left])) { return ; } //  @note already done

          $method = "affiche_".$this->lignes[$left]['type'];
          if (method_exists($this, $method)){
              $this->lignes[$left] = $this->$method($this->lignes[$left]["left"]);
          } else {
              print __CLASS__." lack a method to process $method ($left)\n";
              print_r( $this->lignes[$left]);
              die();
          }
    }

    function affiche_affectation($left) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (!isset($this->lignes[$ligne['left']])) { continue; }

                $retour[] = $this->lignes[$ligne['left']];
                unset($this->lignes[$ligne['left']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }
    function affiche_arginit($left) {
        return __METHOD__;
    }

    function affiche_arglist($left) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (!isset($this->lignes[$ligne['left']])) { continue; }
                $retour[] = $this->lignes[$ligne['left']];
                unset($this->lignes[$ligne['left']]);
            }
        }
        $r = '('.join(', ',$retour).')';
        return $r;
    }

    function affiche_block($left) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                if (!isset($this->lignes[$ligne['left']])) { continue; }

                $this->traite($ligne['left']);
                $retour[] = $this->lignes[$ligne['left']];
                unset($this->lignes[$ligne['left']]);
            }
        }
        $r = "{\n".join(";\n",$retour)."\n}";
        return $r;
    }

    function affiche__break($left) {
        if ($this->lignes[$left]['left'] + 1 == $this->lignes[$left]['right']) {
            return "break 1;"; 
            // @doc implict level : break uses the default value
        } else {
            return "break ".$this->lignes[$left + 1]['code'].";";
        }
    }

    function affiche__case($left) {
        return __METHOD__;
    }

    function affiche_cast($left) {
         $this->traite($left + 1);
         $expr = $this->lignes[$left + 1];
         $retour =  $this->lignes[$left]['code']." {$expr}";
         unset($this->lignes[$left + 1]);

         return $retour; 
    }

    function affiche_comparison($left) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (!isset($this->lignes[$ligne['left']])) { continue; }

                $retour[] = $this->lignes[$ligne['left']];
                unset($this->lignes[$ligne['left']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }
    
    function affiche_concatenation($left) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (isset($this->lignes[$ligne['left']])) {
                    $retour[] = $this->lignes[$ligne['left']];
                    unset($this->lignes[$ligne['left']]);
                }
            }
        }
        return join('.', $retour);
    }

    function affiche_constante($left) {
        return $this->lignes[$left]['code'];
    }

    function affiche__continue($left) {
        if ($this->lignes[$left]['left'] + 1 == $this->lignes[$left]['right']) {
            return "continue 1;"; 
            // @doc implicit continue : default value
        } else {
            return "continue ".$this->lignes[$left + 1]['code'].";";
        }
    }

    function affiche__default($left) {
        return __METHOD__;
    }

    function affiche__for($left) {
        return __METHOD__;
    }
    
    function affiche__foreach($left) {
        return __METHOD__;
    }

    function affiche__function($left) {
        return __METHOD__;
    }
    
    function affiche_functioncall($left) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (isset($this->lignes[$ligne['left']])) {
                    $retour[] = $this->lignes[$ligne['left']];
                    unset($this->lignes[$ligne['left']]);
                }
            }
        }
        // @doc a name, and an argument list
        $r = $retour[0].$retour[1];
        return $r;
    }

    function affiche_ifthen($left) {
    // @todo this only process simple ifthen. 
        $suivant = $this->lignes[$left + 1]['right'] + 1 ;
        $this->traite($left + 1); 
        $this->traite($suivant); 
        return " if ".$this->lignes[$left + 1]." \n".$this->lignes[$suivant]."\n";
    }

    function affiche_inclusion($left) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (isset($this->lignes[$ligne['left']])) {
                    $retour[] = $this->lignes[$ligne['left']];
                    unset($this->lignes[$ligne['left']]);
                }
            }
        }
        // doc a name, and an argument list
        $r = $this->lignes[$left]['code'].'('.$retour[0].')';
        return $r;    
    }
    
    function affiche_literals($left) {
        return "'".$this->lignes[$left]['code']."'";
    }

    function affiche_logique($left) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (!isset($this->lignes[$ligne['left']])) { continue; }

                $retour[] = $this->lignes[$ligne['left']];
                unset($this->lignes[$ligne['left']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }

    function affiche_method($left) {
        $suivant = $this->lignes[$left + 1]['right'] + 1 ;
        $this->traite($left + 1); 
        $this->traite($suivant); 
        return "".$this->lignes[$left + 1]['code']."->".$this->lignes[$suivant];
    }

    function affiche_method_static($left) {
        return __METHOD__;
    }

    function affiche__new($left) {
        $this->traite($left + 1); 
        return " new ".$this->lignes[$left + 1]['code']."";
    }

    function affiche_noscream($left) {
         $this->traite($left + 1);
         $expr = $this->lignes[$left + 1];
         $retour = "@{$expr}";
         unset($this->lignes[$left + 1]);

         return $retour; 
    }

    function affiche_not($left) {
        return __METHOD__;
    }

    function affiche_opappend($left) {
        $this->traite($left + 1); 
        return " ".$this->lignes[$left + 1]['code']."[]";
    }

    function affiche_operation($left) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (!isset($this->lignes[$ligne['left']])) { continue; }

                $retour[] = $this->lignes[$ligne['left']];
                unset($this->lignes[$ligne['left']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }
    
    function affiche_parentheses($left) {
        $this->traite($left + 1); 
        return "(".$this->lignes[$left + 1]['code'].")";
    }

    function affiche_property($left) {
        $this->traite($left + 1); 
        $this->traite($left + 3); 
        $retour = "".$this->lignes[$left + 1]."->".$this->lignes[$left + 3];
        unset($this->lignes[$left + 1]);
        unset($this->lignes[$left + 3]);
        return $retour; 
    }

    function affiche_postplusplus($left) {
         $this->traite($left + 1);
         $expr = $this->lignes[$left + 1];
         $retour = "{$expr}++";
         unset($this->lignes[$left + 1]);

         return $retour; 
    }

    function affiche_rawtext($left) {
        return __METHOD__;
    }

    function affiche__return($left) {
        if ($this->lignes[$left]['left'] + 1 == $this->lignes[$left]['right']) {
            return "return NULL;"; 
            // @doc implicit NULL : return was alone.
        } else {
            return "return ".$this->lignes[$left + 1]['code'].";";
        }
    }

    function affiche_signe($left) {
        return __METHOD__;
    }

    function affiche__switch($left) {
        return __METHOD__;
    }

    function affiche__array($left) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['right'] < $this->lignes[$left]['right'] &&
                $ligne['left'] > $this->lignes[$left]['left']) {
                $this->traite($ligne['left']);
                if (isset($this->lignes[$ligne['left']])) {
                    $retour[] = $this->lignes[$ligne['left']];
                }
            }
        }
        $r = array_shift($retour).'['.join('][', $retour).']';
        return $r;
    }

    function affiche_token_traite($left) {
        return $this->lignes[$left]['code'];
    }

    function affiche_variable($left) {
        return $this->lignes[$left]['code'];
    }
    
    function affiche__while($left) {
        $suivant = $this->lignes[$left + 1]['right'] + 1 ;
        $this->traite($left + 1); 
        $this->traite($suivant); 
        return " while ".$this->lignes[$left + 1]." \n".$this->lignes[$suivant]."\n";
    }

}

?>