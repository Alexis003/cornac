<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
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
    
    function rendu($droite, $gauche, $fichier) {
        $this->fichier = $fichier;
        $sql_fichier = $this->mid->quote($fichier);

        $query = <<<SQL
SELECT * FROM rd
WHERE droite >= $droite AND 
      gauche <= $gauche AND 
      fichier = $sql_fichier 
ORDER BY droite
SQL;
        $res = $this->mid->query($query);
        
        $this->lignes = array();
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->lignes[$ligne['droite']] = $ligne;
            if (!isset($debut)) {
                $debut = $ligne['droite'];
            }
        }
        
        if (!isset($debut)) {
            print "$droite $gauche, $fichier\n$query\n";
            return '';
            die();
        }

        $this->traite($debut);
        return $this->lignes[$debut];
    }
    
    function traite($droite) {
        if (!isset($this->lignes[$droite])) { return ; } // @note already done
        if (is_string($this->lignes[$droite])) { return ; } //  @note already done

          $method = "affiche_".$this->lignes[$droite]['type'];
          if (method_exists($this, $method)){
              $this->lignes[$droite] = $this->$method($this->lignes[$droite]["droite"]);
          } else {
              print __CLASS__." lack a method to process $method ($droite)\n";
              print_r( $this->lignes[$droite]);
              die();
          }
    }

    function affiche_affectation($droite) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (!isset($this->lignes[$ligne['droite']])) { continue; }

                $retour[] = $this->lignes[$ligne['droite']];
                unset($this->lignes[$ligne['droite']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }
    function affiche_arginit($droite) {
        return __METHOD__;
    }

    function affiche_arglist($droite) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (!isset($this->lignes[$ligne['droite']])) { continue; }
                $retour[] = $this->lignes[$ligne['droite']];
                unset($this->lignes[$ligne['droite']]);
            }
        }
        $r = '('.join(', ',$retour).')';
        return $r;
    }

    function affiche_block($droite) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                if (!isset($this->lignes[$ligne['droite']])) { continue; }

                $this->traite($ligne['droite']);
                $retour[] = $this->lignes[$ligne['droite']];
                unset($this->lignes[$ligne['droite']]);
            }
        }
        $r = "{\n".join(";\n",$retour)."\n}";
        return $r;
    }

    function affiche__break($droite) {
        if ($this->lignes[$droite]['droite'] + 1 == $this->lignes[$droite]['gauche']) {
            return "break 1;"; 
            // @doc implict level : break uses the default value
        } else {
            return "break ".$this->lignes[$droite + 1]['code'].";";
        }
    }

    function affiche__case($droite) {
        return __METHOD__;
    }

    function affiche_cast($droite) {
         $this->traite($droite + 1);
         $expr = $this->lignes[$droite + 1];
         $retour =  $this->lignes[$droite]['code']." {$expr}";
         unset($this->lignes[$droite + 1]);

         return $retour; 
    }

    function affiche_comparaison($droite) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (!isset($this->lignes[$ligne['droite']])) { continue; }

                $retour[] = $this->lignes[$ligne['droite']];
                unset($this->lignes[$ligne['droite']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }
    
    function affiche_concatenation($droite) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (isset($this->lignes[$ligne['droite']])) {
                    $retour[] = $this->lignes[$ligne['droite']];
                    unset($this->lignes[$ligne['droite']]);
                }
            }
        }
        return join('.', $retour);
    }

    function affiche_constante($droite) {
        return $this->lignes[$droite]['code'];
    }

    function affiche__continue($droite) {
        if ($this->lignes[$droite]['droite'] + 1 == $this->lignes[$droite]['gauche']) {
            return "continue 1;"; 
            // @doc implicit continue : default value
        } else {
            return "continue ".$this->lignes[$droite + 1]['code'].";";
        }
    }

    function affiche__default($droite) {
        return __METHOD__;
    }

    function affiche__for($droite) {
        return __METHOD__;
    }
    
    function affiche__foreach($droite) {
        return __METHOD__;
    }

    function affiche__function($droite) {
        return __METHOD__;
    }
    
    function affiche_functioncall($droite) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (isset($this->lignes[$ligne['droite']])) {
                    $retour[] = $this->lignes[$ligne['droite']];
                    unset($this->lignes[$ligne['droite']]);
                }
            }
        }
        // @doc a name, and an argument list
        $r = $retour[0].$retour[1];
        return $r;
    }

    function affiche_ifthen($droite) {
    // @todo this only process simple ifthen. 
        $suivant = $this->lignes[$droite + 1]['gauche'] + 1 ;
        $this->traite($droite + 1); 
        $this->traite($suivant); 
        return " if ".$this->lignes[$droite + 1]." \n".$this->lignes[$suivant]."\n";
    }

    function affiche_inclusion($droite) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (isset($this->lignes[$ligne['droite']])) {
                    $retour[] = $this->lignes[$ligne['droite']];
                    unset($this->lignes[$ligne['droite']]);
                }
            }
        }
        // doc a name, and an argument list
        $r = $this->lignes[$droite]['code'].'('.$retour[0].')';
        return $r;    
    }
    
    function affiche_literals($droite) {
        return "'".$this->lignes[$droite]['code']."'";
    }

    function affiche_logique($droite) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (!isset($this->lignes[$ligne['droite']])) { continue; }

                $retour[] = $this->lignes[$ligne['droite']];
                unset($this->lignes[$ligne['droite']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }

    function affiche_method($droite) {
        $suivant = $this->lignes[$droite + 1]['gauche'] + 1 ;
        $this->traite($droite + 1); 
        $this->traite($suivant); 
        return "".$this->lignes[$droite + 1]['code']."->".$this->lignes[$suivant];
    }

    function affiche_method_static($droite) {
        return __METHOD__;
    }

    function affiche__new($droite) {
        $this->traite($droite + 1); 
        return " new ".$this->lignes[$droite + 1]['code']."";
    }

    function affiche_noscream($droite) {
         $this->traite($droite + 1);
         $expr = $this->lignes[$droite + 1];
         $retour = "@{$expr}";
         unset($this->lignes[$droite + 1]);

         return $retour; 
    }

    function affiche_not($droite) {
        return __METHOD__;
    }

    function affiche_opappend($droite) {
        $this->traite($droite + 1); 
        return " ".$this->lignes[$droite + 1]['code']."[]";
    }

    function affiche_operation($droite) {
        $retour = array();

        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (!isset($this->lignes[$ligne['droite']])) { continue; }

                $retour[] = $this->lignes[$ligne['droite']];
                unset($this->lignes[$ligne['droite']]);
            }
        }
        $r = join(' ',$retour).' ';
        return $r;
    }
    
    function affiche_parentheses($droite) {
        $this->traite($droite + 1); 
        return "(".$this->lignes[$droite + 1]['code'].")";
    }

    function affiche_property($droite) {
        $this->traite($droite + 1); 
        $this->traite($droite + 3); 
        $retour = "".$this->lignes[$droite + 1]."->".$this->lignes[$droite + 3];
        unset($this->lignes[$droite + 1]);
        unset($this->lignes[$droite + 3]);
        return $retour; 
    }

    function affiche_postplusplus($droite) {
         $this->traite($droite + 1);
         $expr = $this->lignes[$droite + 1];
         $retour = "{$expr}++";
         unset($this->lignes[$droite + 1]);

         return $retour; 
    }

    function affiche_rawtext($droite) {
        return __METHOD__;
    }

    function affiche__return($droite) {
        if ($this->lignes[$droite]['droite'] + 1 == $this->lignes[$droite]['gauche']) {
            return "return NULL;"; 
            // @doc implicit NULL : return was alone.
        } else {
            return "return ".$this->lignes[$droite + 1]['code'].";";
        }
    }

    function affiche_signe($droite) {
        return __METHOD__;
    }

    function affiche__switch($droite) {
        return __METHOD__;
    }

    function affiche_tableau($droite) {
        $retour = array();
        foreach($this->lignes as $ligne) {
            if ($ligne['gauche'] < $this->lignes[$droite]['gauche'] &&
                $ligne['droite'] > $this->lignes[$droite]['droite']) {
                $this->traite($ligne['droite']);
                if (isset($this->lignes[$ligne['droite']])) {
                    $retour[] = $this->lignes[$ligne['droite']];
                }
            }
        }
        $r = array_shift($retour).'['.join('][', $retour).']';
        return $r;
    }

    function affiche_token_traite($droite) {
        return $this->lignes[$droite]['code'];
    }

    function affiche_variable($droite) {
        return $this->lignes[$droite]['code'];
    }
    
    function affiche__while($droite) {
        $suivant = $this->lignes[$droite + 1]['gauche'] + 1 ;
        $this->traite($droite + 1); 
        $this->traite($suivant); 
        return " while ".$this->lignes[$droite + 1]." \n".$this->lignes[$suivant]."\n";
    }

}

?>