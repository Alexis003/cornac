<?php
//select * from tokens where fichier = 'References/optima4/include/StockAdressage/StockAdressageProcess.inc' and type='ifthen'
//select * from tokens where fichier = 'References/optima4/include/Refs/Reference.inc' and droite > 6409 and gauche < 6422 order by droite;

class rendu {
    function __construct($mid) {
        $this->mid = $mid;
    }
    
    function rendu($droite, $gauche, $fichier) {
        $this->fichier = $fichier;
        
        $requete = "select * from tokens where droite >= $droite and gauche <= $gauche and fichier = ".$this->mid->quote($fichier).' order by droite';
        $res = $this->mid->query($requete);
        
        $this->lignes = array();
        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->lignes[$ligne['droite']] = $ligne;
            if (!isset($debut)) {
                $debut = $ligne['droite'];
            }
        }
        
        if ($fichier == 'References/optima4/include/Refs/Reference.inc') {
//            print_r($this->lignes);
//            die();
        }

        $this->traite($debut);
        return $this->lignes[$debut];
    }
    
    function traite($droite) {
        if (!isset($this->lignes[$droite])) { return ; } // deja fait 
        if (is_string($this->lignes[$droite])) { return ; } // deja fait 

          $methode = "affiche_".$this->lignes[$droite]['type'];
//          print $methode." [$droite]\n";
//          if (!isset($this->lignes[3090])) { print "Plus de 3090]\n"; } else { print "3090 : ok]\n";}
          if (method_exists($this, $methode)){
              $this->lignes[$droite] = $this->$methode($this->lignes[$droite]["droite"]);
          } else {
              print __CLASS__." manque d'une methode pour traiter $methode ($droite)\n";
              print_r( $this->lignes[$droite]);
              die();
          }
//          print $methode." ($droite) = \"{$this->lignes[$droite]}\"\n";
//          if (!isset($this->lignes[3090])) { print "Plus de 3090\n"; } else { print "3090 : ok\n";}
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
            // implicite continue, le return ne contenait pas de valeur
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
            // implicite continue, le return ne contenait pas de valeur
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
        // un nom et une liste d'arguments
        $r = $retour[0].$retour[1];
        return $r;
    }

    function affiche_ifthen($droite) {
    // @attention : ne traite que les ifthen simples
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
        // un nom et une liste d'arguments
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
        $this->traite($droite + 1); // functioncall
        $this->traite($suivant); // functioncall
        return "".$this->lignes[$droite + 1]['code']."->".$this->lignes[$suivant];
    }

    function affiche_method_static($droite) {
        return __METHOD__;
    }

    function affiche__new($droite) {
        $this->traite($droite + 1); // functioncall
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
        $this->traite($droite + 1); // functioncall
        return "(".$this->lignes[$droite + 1]['code'].")";
    }

    function affiche_property($droite) {
        $this->traite($droite + 1); // objet
        $this->traite($droite + 3); // propriété
        $retour = "".$this->lignes[$droite + 1]."->".$this->lignes[$droite + 3];
// @question : je ne comprend pas pourquoi ci-dessous ne marche pas
//        $retour = "".$this->lignes[$droite + 1]['code']."->".$this->lignes[$droite + 3];
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
            // implicite NULL, le return ne contenait pas de valeur
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