<?php

class template_db extends template {
    protected $ligne = 0;
    protected $scope = 'global';
    protected $class = '';
    
    protected $table = 'tokens';
    protected $tags = array();
    
    function __construct($root, $fichier = null) {
        parent::__construct();
    }
    
    function save($filename = null) {
        print "Sauvé en base\n";
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if ($niveau > 200) {
            print_r(xdebug_get_function_stack());        
            print "Attention : plus de 100 niveaux de récursion (annulation)\n"; die();
        }
        if (is_null($noeud)) {
            if ($niveau == 0) {
                $noeud = $this->root;
            } else {
                print_r(xdebug_get_function_stack());        
                print "On a tenté de refiler un null à affiche.";
                die();
            }
        }
        
        if (!is_object($noeud)) {
            print_r(xdebug_get_function_stack());        
            print "Attention, $noeud n'est pas un objet (".gettype($noeud).")\n";
            die(__METHOD__."\n");
        }
        $class = get_class($noeud);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $retour = $this->$method($noeud, $niveau + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
        }

        return $retour;
    }
    
////////////////////////////////////////////////////////////////////////
// database functions
////////////////////////////////////////////////////////////////////////

    private static $ids = 0;
    
    function getNextId() {
        return $this->ids++;
    }

    private static $intervallaire = 0;
    
    function getIntervalleId() {
        return $this->intervallaire++;
    }

    function saveNoeud($noeud) {
        global $fichier;
        
        if (($noeud->getligne() + 0) > 0) {
            $this->ligne = $noeud->getligne() + 0;
        } 
        
        $requete = "INSERT INTO {$this->table} VALUES 
            (NULL ,
             '".$noeud->myDroite."',
             '".$noeud->myGauche."',
             '".get_class($noeud)."',
             ".$this->database->quote($noeud->getCode()).",
             '$fichier',
             ". $this->ligne .",
             '". $this->scope ."',
             '". $this->class ."'
             )";
             

        $this->database->query($requete);
        if ($this->database->errorCode() != 0) {
            print $requete."\n";
            print_r($this->database->errorInfo());
            die();
        }
        
        $retour = $this->database->lastinsertid();
        
        if (is_array($this->tags) && count($this->tags) > 0) {
            foreach($this->tags as $label => $tokens) {
                foreach($tokens as $token) {
                    $requete = "INSERT INTO {$this->table_tags} VALUES 
                    ($retour ,
                     '".$token."',
                     '".$label."')";
    
                    $this->database->query($requete);
                    if ($this->database->errorCode() != 0) {
                        print $requete."\n";
                        print_r($this->database->errorInfo());
                        die();
                    }
                }
            }
        }
        
        $this->tags = array();
        $noeud->database_id = $retour;
        
        return $retour;
    }

////////////////////////////////////////////////////////////////////////
// database functions
////////////////////////////////////////////////////////////////////////
    function affiche_token_traite($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_affectation($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $tags = array();
        $tags['left'][] = $this->affiche($noeud->getDroite(), $niveau + 1);
        $tags['operator'][] = $this->affiche($noeud->getOperateur(), $niveau + 1);
        $tags['right'][] = $this->affiche($noeud->getGauche(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche_arginit($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getValeur(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_arglist($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $elements = $noeud->getList();
        if (count($elements) == 0) {
            $x = new token_traite(new Token());
            $this->affiche($x, $niveau + 1);
            return;
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    //rien
                } else {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_block($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        if ($noeud->checkCode('{')) {
            $noeud->setCode('');
        }

        $elements = $noeud->getList();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        $noeud->myGauche = $this->getIntervalleId();
        $retour = $this->saveNoeud($noeud);
        return $retour;
    }

    function affiche__break($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getNiveaux(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__case($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        if (!is_null($m = $noeud->getComparant())) {
            $this->affiche($m, $niveau + 1);
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_cast($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__catch($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getException(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__continue($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getNiveaux(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }
    
    function affiche_cdtternaire($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getVraie(), $niveau + 1);
        $this->affiche($noeud->getFaux(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_codephp($noeud, $niveau) {
        //le seul autorisé
        if (!isset($noeud->dotId)) {
            $noeud->dotId = $this->getNextId();
        }
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getphp_code(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__class($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');
        $classe_precedent = $this->class;
        $this->class = $noeud->getNom()->getCode();

        $tags = array();
        $abstract = $noeud->getAbstract();
        if(!is_null($abstract)) {
            $tags['abstract'][] = $this->affiche($abstract, $niveau + 1);            
        }

        $tags['name'][] = $this->affiche($noeud->getNom(), $niveau + 1);            

        $extends = $noeud->getExtends();
        if (!is_null($extends)) {
            $tags['extends'][] = $this->affiche($extends, $niveau + 1);            
        }

        $implements = $noeud->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $implement) {
                $tags['implements'][] =  $this->affiche($implement, $niveau + 1);            
            }
        }

        $tags['block'][] = $this->affiche($noeud->getBlock(), $niveau + 1);            

        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        $res = $this->saveNoeud($noeud);
        $this->class = $classe_precedent;
        return $res;
    }

    function affiche__clone($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_clevaleur($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCle(), $niveau + 1);
        $this->affiche($noeud->getValeur(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_comparaison($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_concatenation($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $elements = $noeud->getList();
        $labels = array();

        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);            
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_constante($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_constante_static($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $classe = $noeud->getClass();
        $this->affiche($classe, $niveau + 1);
        $methode = $noeud->getConstant();
        $this->affiche($methode, $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_constante_classe($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $classe = $noeud->getName();
        $this->affiche($classe, $niveau + 1);
        $methode = $noeud->getConstante();
        $this->affiche($methode, $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

   function affiche_decalage($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }
    
    function affiche__default($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__for($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        if (!is_null($f = $noeud->getInit())) {
            $this->affiche($f, $niveau + 1);
        }
        if (!is_null($f = $noeud->getFin())) {
            $this->affiche($f, $niveau + 1);
        }
        if (!is_null($f = $noeud->getIncrement())) {
            $this->affiche($f, $niveau + 1);
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__foreach($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $tags = array();
        $tags['array'][] = $this->affiche($noeud->getTableau(), $niveau + 1);

        $key = $noeud->getKey();
        if (!is_null($key)) {
           $tags['key'][] = $this->affiche($key, $niveau + 1);
        }
        $tags['value'][] = $this->affiche($noeud->getValue(), $niveau + 1);
        $tags['block'][] = $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche__function($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $scope_precedent = $this->scope;
        $this->scope = $noeud->getName()->getCode();

        $tags = array();
        if (!is_null($m = $noeud->getVisibility())) {
            $tags['visibility'][] = $this->affiche($m, $niveau + 1);
        }
        if (!is_null($m = $noeud->getAbstract())) {
            $tags['abstract'][] = $this->affiche($m, $niveau + 1);
        }
        if (!is_null($m = $noeud->getStatic())) {
            $tags['static'][] = $this->affiche($m, $niveau + 1);
        }
        $tags['name'][] = $this->affiche($noeud->getName(), $niveau + 1);
        // récupération du nom de la fonction
        $noeud->setCode($noeud->getName()->getCode());
        $tags['args'][] = $this->affiche($noeud->getArgs(), $niveau + 1);
        $tags['block'][] = $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();

        $this->tags = $tags;
        $res = $this->saveNoeud($noeud);
        $this->scope = $scope_precedent;
        return $res;
    }

    function affiche_functioncall($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $tags = array();
        $tags['fonction'][] = $this->affiche($noeud->getFunction(), $niveau + 1);
        $tags['args'][] = $this->affiche($noeud->getArgs(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche__global($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $elements = $noeud->getVariables();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);    
    }

    function affiche_ifthen($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();
        $labels = array();

        $tags = array();
        
        foreach($conditions as $id => &$condition) {
            $condition->setCode('elseif');
            $tags['condition'][] = $this->affiche($condition, $niveau + 1);
            $tags['then'][] = $this->affiche($thens[$id], $niveau + 1);
        }
        
        $else = $noeud->getElse();
        if (!is_null($else)){
            $else->setCode('else');
            $tags['else'][] = $this->affiche($else, $niveau + 1);
        }

        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);
    }

    function affiche_inclusion($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getInclusion(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche__interface($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $classe_precedent = $this->class;
        $this->class = $noeud->getName()->getCode();

        $e = $noeud->getExtends();
        if (count($e) > 0) {
            foreach($e as $ex) {
                $this->affiche($ex, $niveau + 1);
            }
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        $res = $this->saveNoeud($noeud);
        $this->class = $classe_precedent;
        return $res;
    }

    function affiche_invert($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_literals($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_logique($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_method($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['objet'][] = $this->affiche($noeud->getObject(), $niveau + 1);
        $tags['methode'][] = $this->affiche($noeud->getMethod(), $niveau + 1);        
        
        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);        
    }

    function affiche_method_static($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['classe'][] = $this->affiche($noeud->getClass(), $niveau + 1);
        $tags['methode'][] = $this->affiche($noeud->getMethod(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);        
    }

    function affiche__new($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['name'][] = $this->affiche($noeud->getClasse(), $niveau + 1);
        $tags['args'][] = $this->affiche($noeud->getArgs(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);        
    }

    
    function affiche_noscream($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_not($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_opappend($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_operation($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperation(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_parentheses($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getContenu(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);        
    }

    function affiche_preplusplus($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);    
    }

    function affiche_postplusplus($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);    
    }

    function affiche_property($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
//        $this->affiche($noeud->getObject(), $niveau + 1);
//        $this->affiche($noeud->getProperty(), $niveau + 1);

        $tags = array();
        $tags['object'][] = $this->affiche($noeud->getObject(), $niveau + 1);
        $tags['property'][] = $this->affiche($noeud->getProperty(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);        
    }

    function affiche_property_static($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $this->affiche($noeud->getClass(), $niveau + 1);
        $this->affiche($noeud->getProperty(), $niveau + 1);
        
        $tags = array();
        $tags['class'][] = $this->affiche($noeud->getClass(), $niveau + 1);
        $tags['property'][] = $this->affiche($noeud->getProperty(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->saveNoeud($noeud);        
    }

    function affiche_rawtext($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_reference($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getExpression(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__return($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        if (!is_null($retour = $noeud->getRetour())) {
            $this->affiche($retour, $niveau + 1);
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_sequence($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $elements = $noeud->getElements();
        if (count($elements) == 0) {
            // rien
        } else {
            $labels = array();
            $id = 0;
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    die("cas de l'argument null ou inexistant dans une sequence");
                } else {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_signe($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__static($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getExpression(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__switch($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getOperande(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_tableau($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__throw($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getException(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

/*
    function affiche_token_traite($noeud, $niveau) {
        print get_class($noeud);
    
        print str_repeat('  ', $niveau).$noeud->getCode()." \n";
    }
*/

    function affiche__try($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getBlock(), $niveau + 1);

        $elements = $noeud->getCatch();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        
        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_typehint($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');

        $this->affiche($noeud->getType(), $niveau + 1);
        $this->affiche($noeud->getNom(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__var($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        $noeud->setCode('');
        
        if (!is_null($noeud->getVisibility())) {
            $this->affiche($noeud->getVisibility(), $niveau + 1);
        }
        if (!is_null($noeud->getStatic())) {
            $this->affiche($noeud->getStatic(), $niveau + 1);
        }
        $variables = $noeud->getVariable();
        if (count($variables) > 0) {
            $inits = $noeud->getInit();
            foreach($variables as $id => $variable) {
                $this->affiche($variable, $niveau + 1);
                if (!is_null($inits[$id])) {
                    $this->affiche($inits[$id], $niveau + 1);
                }
            }
        
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche_variable($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();
        
        $nom = $noeud->getNom();
        if (is_object($nom)) {
            $this->affiche($nom, $niveau + 1);
            $noeud->setCode("$".$nom->getCode());
        }

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__while($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }

    function affiche__dowhile($noeud, $niveau) {
        $noeud->myId = $this->getNextId();
        $noeud->myDroite = $this->getIntervalleId();

        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->myGauche = $this->getIntervalleId();
        return $this->saveNoeud($noeud);
    }
    
    function affiche_Token($noeud, $niveau) {
        print_r(xdebug_get_function_stack());        
        print "Attention, Token affiché : '$noeud'\n";
        die();
    }
}

?>