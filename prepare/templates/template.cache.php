<?php

class template_cache extends template {
    protected $root = null;
    private $database = null;
    private $ligne = 0;
    private $scope = 'global';
    private $class = '';
    
    private $table = 'tokens';
    private $tags = array();
    
    function __construct($root, $fichier = null) {
        parent::__construct();
        
        global $INI;
        $this->table = $INI['cornac']['prefix'] ?: 'tokens';

        
        if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
            $this->database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);

            $this->database->query('DELETE FROM '.$this->table.'_cache WHERE fichier = "'.$fichier.'"');
            $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_cache (
                                                          id       INTEGER PRIMARY KEY AUTO_INCREMENT, 
                                                          code     VARCHAR(255),
                                                          fichier  VARCHAR(255)
                                                          )');
        } elseif (isset($INI['sqlite']) && $INI['sqlite']['active'] == true) {
            $this->database = new pdo($INI['sqlite']['dsn']);

            $this->database->query('DELETE FROM '.$this->table.'_cache WHERE fichier = "'.$fichier.'"');
            $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_cache (
                                                          id       INTEGER PRIMARY KEY AUTOINCREMENT, 
                                                          code     VARCHAR(255),
                                                          fichier  VARCHAR(255)
                                                          )');
        } else {
            print "No database configuration provided (no db)\n";
            die();
        }
        
        $this->table_tags = $this->table.'_tags';

        $this->root = $root;
    }
    
    function save($filename = null) {
        print "Cache mis à jour\n";
        unset($this->database);
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if ($niveau > 200) {
            print_r(xdebug_get_function_stack());        
            print "Attention : plus de 100 niveaux de récursion (annulation) ".__METHOD__."\n"; die();
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
            die(__METHOD__);
        }
        $class = get_class($noeud);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $return = $this->$method($noeud, $niveau + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
        }

        return $return;
    }
    
////////////////////////////////////////////////////////////////////////
// database functions
////////////////////////////////////////////////////////////////////////
    function saveNoeud($noeud) {
        global $file;
        
        $requete = "INSERT INTO {$this->table}_cache VALUES 
            (
             '".$noeud->database_id."',
             ".$this->database->quote($noeud->cache).",
             ".$this->database->quote($file)."
             )";
             

        $this->database->query($requete);
        if ($this->database->errorCode() != 0) {
            print $requete."\n";
            print_r($this->database->errorInfo());
            die();
        }

        return TRUE;
    }
////////////////////////////////////////////////////////////////////////
// database functions
////////////////////////////////////////////////////////////////////////
    function affiche_token_traite($noeud, $niveau) {
        $noeud->cache = $noeud->getCode();
    }

    function affiche_affectation($noeud, $niveau) {
        $droite = $noeud->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $noeud->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $noeud->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $noeud->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->saveNoeud($noeud);
    }

    function affiche_arginit($noeud, $niveau) {
        $var = $noeud->getVariable();
        $this->affiche($var, $niveau + 1);
        $valeur = $noeud->getValeur();
        $this->affiche($valeur, $niveau + 1);
        
        $noeud->cache = $var->cache." = ".$valeur->cache;
        return $this->saveNoeud($noeud);
    }

    function affiche_arglist($noeud, $niveau) {
        $elements = $noeud->getList();
        if (count($elements) == 0) {
            $x = new token_traite(new Token());
            $this->affiche($x, $niveau + 1);
            $noeud->cache = '()';
            return;
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    $labels[] = '';
                    //rien
                } else {
                    $this->affiche($e, $niveau + 1);
                    if (!isset($e->cache)) {
                        print $e;
                        die();
                    }
                    $labels[] = $e->cache;
                }
            }
            $noeud->cache = '('.join(', ', $labels).')';
        }
    }

    function affiche_block($noeud, $niveau) {
        $elements = $noeud->getList();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        
        $noeud->cache = ' { block }';
    }

    function affiche__break($noeud, $niveau) {
        $niveaux = $noeud->getNiveaux();
        $this->affiche($niveaux, $niveau + 1);

        $noeud->cache = 'break '.$niveaux->cache;
    }

    function affiche__case($noeud, $niveau) {
        $case = 'case';
        if (!is_null($m = $noeud->getComparant())) {
            $this->affiche($m, $niveau + 1);
            $case .= " ".$m->cache;
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);
        // on ignore

        $noeud->cache = $case;
        return $this->saveNoeud($noeud);
    }

    function affiche_cast($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);

        $noeud->cache = '('.$expression->cache.')';
    }

    function affiche__catch($noeud, $niveau) {
        $this->affiche($noeud->getException(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
        
        $noeud->cache = 'catch';
    }

    function affiche__continue($noeud, $niveau) {
        $niveaux = $noeud->getNiveaux();
        $this->affiche($niveaux, $niveau + 1);

        $noeud->cache = 'continue '.$niveaux->cache;
    }
    
    function affiche_cdtternaire($noeud, $niveau) {
        $condition = $noeud->getCondition();
        $this->affiche($condition, $niveau + 1);

        $vraie = $noeud->getVraie();
        $this->affiche($vraie, $niveau + 1);
        
        $faux = $noeud->getFaux();
        $this->affiche($faux, $niveau + 1);

        $noeud->cache = $condition.' ? '.$vraie.' : '.$faux;
        return $this->saveNoeud($noeud);
    }

    function affiche_codephp($noeud, $niveau) {
        $this->affiche($noeud->getphp_code(), $niveau + 1);
        $noeud->cache = "<?php ?>";
    }

    function affiche__class($noeud, $niveau) {
        $name = $noeud->getNom();
        $this->affiche($name, $niveau + 1);            
        $class = ' class '.$name->cache;

        $abstract = $noeud->getAbstract();
        if(!is_null($abstract)) {
            $this->affiche($abstract, $niveau + 1);
            $class = 'abstract '.$class;
        }

        $extends = $noeud->getExtends();
        if (!is_null($extends)) {
            $this->affiche($extends, $niveau + 1);     
            $class .= " extends ".$extends->cache;
        }

        $implements = $noeud->getImplements();
        if (count($implements) > 0) {
            $implemented = array();
            foreach($implements as $implement) {
                $this->affiche($implement, $niveau + 1);
                $implemented[] =  $implement->cache;
            }
            $class .= " implements ".join(', ', $implemented);
        }

        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->cache = $class;
        return $this->saveNoeud($noeud);
    }

    function affiche__clone($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);

        $noeud->cache = 'clone '.$expression->cache;
    }

    function affiche_clevaleur($noeud, $niveau) {
        $cle = $noeud->getCle();
        $this->affiche($cle, $niveau + 1);
        $valeur = $noeud->getValeur();
        $this->affiche($valeur, $niveau + 1);

        $noeud->cache = $cle->cache.' => '.$valeur->cache;
    }

    function affiche_comparaison($noeud, $niveau) {
        $droite = $noeud->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $noeud->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $noeud->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $noeud->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->saveNoeud($noeud);
    }

    function affiche_concatenation($noeud, $niveau) {
        $elements = $noeud->getList();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);            
            $labels[] = $e->cache;
        }
        
        $noeud->cache = join('.', $labels);
        return $this->saveNoeud($noeud);
    }

    function affiche_constante($noeud, $niveau) {
        $noeud->cache = $noeud->getCode();
    }

    function affiche_constante_static($noeud, $niveau) {
        $classe = $noeud->getClass();
        $this->affiche($classe, $niveau + 1);
        $methode = $noeud->getConstant();
        $this->affiche($methode, $niveau + 1);

        $noeud->cache = $classe->cache.'::'.$methode->cache;
        return $this->saveNoeud($noeud);        
    }

    function affiche_constante_classe($noeud, $niveau) {
        $classe = $noeud->getName();
        $this->affiche($classe, $niveau + 1);
        $constante = $noeud->getConstante();
        $this->affiche($constante, $niveau + 1);

        $noeud->cache = $classe->cache.'::'.$constante->cache;
        return $this->saveNoeud($noeud);        
    }

   function affiche_decalage($noeud, $niveau) {
        $droite = $noeud->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $noeud->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $noeud->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $noeud->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->saveNoeud($noeud);
    }

    function affiche__default($noeud, $niveau) {
        $this->affiche($noeud->getBlock(), $niveau + 1);
        $noeud->cache = 'default'; 
    }

    function affiche__for($noeud, $niveau) {
        $noeud->cache = 'foreach(';
        if (!is_null($f = $noeud->getInit())) {
            $this->affiche($f, $niveau + 1);
            $noeud->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $noeud->getFin())) {
            $this->affiche($f, $niveau + 1);
            $noeud->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $noeud->getIncrement())) {
            $this->affiche($f, $niveau + 1);
            $noeud->cache .= $f->cache.')';
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);
        // on ignore le block
    }

    function affiche__foreach($noeud, $niveau) {
        $noeud->cache = 'foreach(';

        $tableau = $noeud->getTableau();
        $this->affiche($tableau, $niveau + 1);
        $noeud->cache .= $tableau->cache.' as ';
        
        $key = $noeud->getKey();
        if (!is_null($key)) {
            $this->affiche($key, $niveau + 1);
            $noeud->cache .= $key.' => ';
        }

        $valeur = $noeud->getValue();
        $this->affiche($valeur, $niveau + 1);
        $noeud->cache .= $valeur.')';

        $this->affiche($noeud->getBlock(), $niveau + 1);
        // on ignore
    }

    function affiche__function($noeud, $niveau) {
        $nom = $noeud->getName();
        $this->affiche($nom, $niveau + 1);
        $args = $noeud->getArgs();
        $tags['args'][] = $this->affiche($args, $niveau + 1);
        $block = $noeud->getBlock();
        $tags['block'][] = $this->affiche($block, $niveau + 1);
        
        $function = 'function '.$nom->cache.$args->cache; 
        // on ignore le block

        if (!is_null($m = $noeud->getVisibility())) {
            $tags['visibility'][] = $this->affiche($m, $niveau + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $noeud->getAbstract())) {
            $tags['abstract'][] = $this->affiche($m, $niveau + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $noeud->getStatic())) {
            $tags['static'][] = $this->affiche($m, $niveau + 1);
            $function = $m->cache." $function";
        }

        $noeud->cache = $function;
        return $this->saveNoeud($noeud);
    }

    function affiche_functioncall($noeud, $niveau) {
        $fonction = $noeud->getFunction();
        $this->affiche($fonction, $niveau + 1);

        $args = $noeud->getArgs();
        $this->affiche($args, $niveau + 1);
        
        $noeud->cache = $fonction->cache.''.$args->cache;
        $this->saveNoeud($noeud);
    }

    function affiche__global($noeud, $niveau) {
        $elements = $noeud->getVariables();
        $labels = array();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
            $labels[] = $e->cache;
        }

        $noeud->cache = 'global '.join(', ', $labels);
        return $this->saveNoeud($noeud);    
    }

    function affiche_ifthen($noeud, $niveau) {
        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();

        foreach($conditions as $id => &$condition) {
            $this->affiche($condition, $niveau + 1);
            $this->affiche($thens[$id], $niveau + 1);
        }
        
        $else = $noeud->getElse();
        if (!is_null($else)){
            $this->affiche($else, $niveau + 1);
        }
        
        $noeud->cache = "<if then>";
    }

    function affiche_inclusion($noeud, $niveau) {
        $inclusion = $noeud->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
        
        $noeud->cache = 'include '.$inclusion->cache;
        return $this->saveNoeud($noeud);        
    }

    function affiche__interface($noeud, $niveau) {
        $cache = array();
        $e = $noeud->getExtends();
        if (count($e) > 0) {
            foreach($e as $ex) {
                $this->affiche($ex, $niveau + 1);
                $cache[] = $ex->cache;
            }
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);

        $noeud->cache = 'interface '.$noeud->getName();
        if (count($e) > 0) {
            $noeud->cache .= 'implements '.join(', ', $cache);
        }
        return $this->saveNoeud($noeud);        
    }

    function affiche_invert($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);

        $noeud->cache = ' '.$expression->cache;
        return $this->saveNoeud($noeud);        
    }

    function affiche_literals($noeud, $niveau) {
        $noeud->cache = $noeud->getCode();
    }

    function affiche_logique($noeud, $niveau) {
        $droite = $noeud->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $noeud->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $noeud->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $noeud->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
    }

    function affiche_method($noeud, $niveau) {
        $object = $noeud->getObject();
        $this->affiche($object, $niveau + 1);
        $method = $noeud->getMethod();
        $this->affiche($method, $niveau + 1);        
        
        $noeud->cache = $object->cache."->".$method->cache;
        $this->saveNoeud($noeud);
    }

    function affiche_method_static($noeud, $niveau) {
        $classe = $noeud->getClass();
        $this->affiche($classe, $niveau + 1);
        $methode = $noeud->getMethod();
        $this->affiche($methode, $niveau + 1);

        $noeud->cache = $classe->cache.'::'.$methode->cache;
        return $this->saveNoeud($noeud);        
    }

    function affiche__new($noeud, $niveau) {
        $name = $noeud->getClasse();
        $tags['name'][] = $this->affiche($name, $niveau + 1);
        $args = $noeud->getArgs();
        $tags['args'][] = $this->affiche($args, $niveau + 1);
        
        $noeud->cache = 'new '.$name->cache.''.$args->cache;
    }
    
    function affiche_noscream($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);

        $noeud->cache = '@'.$expression->cache;
        return $this->saveNoeud($noeud);
    }

    function affiche_not($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);

        $noeud->cache = '!'.$expression->cache;
        return $this->saveNoeud($noeud);
    }

    function affiche_opappend($noeud, $niveau) {
        $variable = $noeud->getVariable();
        $this->affiche($variable, $niveau + 1);

        $noeud->cache = $variable->cache.'[]';
        return $this->saveNoeud($noeud);
    }

    function affiche_operation($noeud, $niveau) {
        $droite = $noeud->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operation = $noeud->getOperation();
        $this->affiche($operation, $niveau + 1);
        $gauche = $noeud->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $noeud->cache = $droite->cache.' '.$operation->cache.' '.$gauche->cache;
    }

    function affiche_parentheses($noeud, $niveau) {
        $contenu = $noeud->getContenu();
        $this->affiche($contenu, $niveau + 1);
        
        $noeud->cache = '('.$contenu->cache.')';
        return $this->saveNoeud($noeud);        
    }

    function affiche_preplusplus($noeud, $niveau) {
        $var = $noeud->getVariable();
        $this->affiche($var, $niveau + 1);
        $operateur = $noeud->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        
        $noeud->cache = $operateur->cache.$var->cache;
        return $this->saveNoeud($noeud);    
    }

    function affiche_postplusplus($noeud, $niveau) {
        $var = $noeud->getVariable();
        $this->affiche($var, $niveau + 1);
        $operateur = $noeud->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        
        $noeud->cache = $var->cache.$operateur->cache;
        return $this->saveNoeud($noeud);    
    }

    function affiche_property($noeud, $niveau) {
        $object = $noeud->getObject();
        $this->affiche($object, $niveau + 1);
        $property = $noeud->getProperty();
        $this->affiche($property, $niveau + 1);
        
        $noeud->cache = $object->cache."->".$property->cache;
        $this->saveNoeud($noeud);
    }

    function affiche_property_static($noeud, $niveau) {
        $classe = $noeud->getClass();
        $this->affiche($classe, $niveau + 1);
        $property = $noeud->getProperty();
        $this->affiche($property, $niveau + 1);
        
        $noeud->cache = $classe->cache."->".$property->cache;
        $this->saveNoeud($noeud);
    }

    function affiche_rawtext($noeud, $niveau) {
        $noeud->cache = '';
    }

    function affiche_reference($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);

        $noeud->cache = '&'.$expression->cache;
        return $this->saveNoeud($noeud);
    }

    function affiche__return($noeud, $niveau) {
        if (!is_null($return = $noeud->getReturn())) {
            $this->affiche($return, $niveau + 1);
            $noeud->cache = 'return '.$return->cache;
        } else {
            $noeud->cache = 'return NULL';
        }

        return $this->saveNoeud($noeud);
    }

    function affiche_sequence($noeud, $niveau) {
        $elements = $noeud->getElements();
        if (count($elements) == 0) {
            // rien
            $noeud->cache = '';
        } else {
            $labels = array();
            $id = 0;
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    die("cas de l'argument null ou inexistant dans une sequence");
                } else {
                    $labels[] = $this->affiche($e, $niveau + 1);
                }
            }
            $noeud->cache = join('', $labels);
        }

    }

    function affiche_signe($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);
        $signe = $noeud->getSigne();
        $this->affiche($signe, $niveau + 1);
        
        $noeud->cache = $signe->cache.$expression->cache;
    }

    function affiche__static($noeud, $niveau) {
        $expression = $noeud->getExpression();
        $this->affiche($expression, $niveau + 1);
        
        $noeud->cache = 'static '.$expression->cache;
    }

    function affiche__switch($noeud, $niveau) {
        $this->affiche($noeud->getOperande(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
        $noeud->cache = '<switch>';
    }

    function affiche_tableau($noeud, $niveau) {
        $variable = $noeud->getVariable();
        $this->affiche($variable, $niveau + 1);
        $index = $noeud->getIndex();
        $this->affiche($index, $niveau + 1);
        
        $noeud->cache = $variable->cache.'['.$index->cache.']';
        return $this->saveNoeud($noeud);
    }

    function affiche__throw($noeud, $niveau) {
        $exception = $noeud->getException();
        $this->affiche($exception, $niveau + 1);

        $noeud->cache = 'throw '.$exception->cache;
    }

    function affiche__try($noeud, $niveau) {
        $block = $noeud->getBlock();
        $this->affiche($block, $niveau + 1);

        $elements = $noeud->getCatch();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
            $labels[]=  $e->cache;
        }
        $noeud->cache = '<try>';        
    }

    function affiche_typehint($noeud, $niveau) {
        $type = $noeud->getType();
        $this->affiche($type, $niveau + 1);
        $nom = $noeud->getNom();
        $this->affiche($nom, $niveau + 1);
        
        $noeud->cache = $type->cache." ".$nom->cache;
    }

    function affiche__var($noeud, $niveau) {
        $var = array();
        
        $variables = $noeud->getVariable();
        if (count($variables) > 0) {
            $inits = $noeud->getInit();
            foreach($variables as $id => $variable) {
                $this->affiche($variable, $niveau + 1);
                $var[] = $variable->cache;
                if (!is_null($inits[$id])) {
                    $this->affiche($inits[$id], $niveau + 1);
                    $var[] = $inits[$id]->cache;
                }
            }
        }
        
        $var = join(', ', $var);

        $visibility = $noeud->getVisibility();
        if (!is_null($visibility)) {
            $visibility = $noeud->getVisibility();
            $this->affiche($visibility, $niveau + 1);
            $var = $visibility->cache." ".$var;
        } else {
            $var = "var $var";
        }

        if (!is_null($noeud->getStatic())) {
            $this->affiche($noeud->getStatic(), $niveau + 1);
            $var = "static $var";
        }
        
        $noeud->cache = $var;
        return $this->saveNoeud($noeud);
    }

    function affiche_variable($noeud, $niveau) {
        $nom = $noeud->getNom();
        if (is_object($nom)) {
            $this->affiche($nom, $niveau + 1);
            $noeud->cache = '$'.$nom->cache;
        } else {
            $noeud->cache = $nom;
        }
        return $this->saveNoeud($noeud);
    }

    function affiche__while($noeud, $niveau) {
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
        $noeud->cache = '<while>';        
    }

    function affiche__dowhile($noeud, $niveau) {
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
        $noeud->cache = '<do_while>';        
    }
    
    function affiche_Token($noeud, $niveau) {
        print_r(xdebug_get_function_stack());        
        print "Attention, Token affiché : '$noeud'\n";
        die();
    }
}

?>