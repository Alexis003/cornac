<?php

// @todo : centraliser les rquêtes SQL 
// @todo : mettre en parmètre 
abstract class modules {
    protected  $occurrences = 0;
    protected  $fichiers_identifies = 0;
    protected  $total_de_fichiers = 0;
    public static    $mid   = null;
    public static    $table = null;
    
    const FORMAT_DEFAULT = 0;
    const FORMAT_HTMLLIST = 1;
    const FORMAT_DOT = 2;
    const FORMAT_SCOPE = 3;

    protected  $format = modules::FORMAT_HTMLLIST;

    function __construct($mid) {
        global $prefixe;
        
        if (!isset($prefixe)) {
           $prefixe = 'tokens';
        }
        
        
        $this->mid = $mid;
        $this->format_export = modules::FORMAT_DEFAULT;
        
        $this->tables = array('<rapport>' => $prefixe.'_rapport',
                              '<rapport_scope>' => $prefixe.'_rapport_scope',
                              '<tokens>' => $prefixe.'',
                              '<tokens_cache>' => $prefixe.'_cache',
                              '<tokens_tags>' => $prefixe.'_tags',
                              '<rapport_module>' => $prefixe.'_rapport_module',
                              '<rapport_dot>' => $prefixe.'_rapport_dot',
                            );
    }
    
    abstract function analyse();

    function getdescription() {
        if (isset($this->description_en)) {
            return $this->description_en ;
        } elseif (isset($this->description)) {
            return $this->description ;
        } else {
            return "Description : ".__CLASS__ ;    
        }
    }

    function getnombre() {
        return $this->occurrences;
    } 
    
    function init_file() {
        setlocale(LC_TIME, "fr_FR");
        $date = strftime("%A %d %B %Y %H:%M:%S ");
        
        $this->export = "<html><body>
        <table>
            <tr><td><a href=\"index.html\">Index</td><td>&nbsp;</td></tr>
            <tr><td>Production</td><td>$date</td></tr>
            <tr><td>Nombre de fichiers</td><td>{$this->total_de_fichiers}</td></tr>
            <tr><td>Nombre de fichiers identifi&eacute;s</td><td>{$this->fichiers_identifies}</td></tr>
            <tr><td>Nombre d'occurrences</td><td>{$this->occurrences}</td></tr>
        </table>
        <p>&nbsp;</p>
        ";
    }

    function finish_file() {
        $this->export .= "
        <table>
            <tr><td><a href=\"index.html\">Index</td><td>&nbsp;</td></tr>
        </table>
        <p>&nbsp;</p>
</body></html>";
    }
    
    function save_file($name) {
        file_put_contents('export/'.$this->getfilename(), $this->export);
    }

    function getfilename() {
        if ($this->format_export == modules::FORMAT_DOT) {
            return $this->name.".dot";
        } else {
            return $this->name.".html";
        }
    }

    function sauve() {
        if ($this->name == __CLASS__) { 
            print "Une classe qui n'a pas donné son nom\n";
            return false;
        }
        
        $now = date('c');
        $this->exec_query("REPLACE INTO <rapport_module> VALUES ('$this->name', '$now', '{$this->format}')");

    }

function array2li($array) {
    $retour = '';
    if (count($array) == 0) { 
        $retour .= "Aucune valeur trouvee";
    } else {
        $retour .= "<ul>";
        foreach($array as $name => $fonctions) {
            if (count($fonctions) == 0) { continue; }
            // @a_revoir
            $name = str_replace('/Users/macbook/Desktop/audit/','',$name);
            if (is_array($fonctions)) {
                $retour .= "<li>$name<ul>";
                asort($fonctions);
                foreach($fonctions as $nom => $nombre) {
                    $retour .= "<li>".$this->highlight_code($nom, true)." : $nombre</li>";
                }
                $retour .= "</ul></li>";
            } else {
                $retour .= "<li>$name : $fonctions</li>";
            }
        }
           $retour .= "</ul>";
    }
    
    return $retour;
}

function array_invert($array) {
    $retour = array();    
    
    foreach($array as $key => $value) {
        foreach($value as $k => $v) {
            $retour[$k][] = $key;
        }
    }
    
    return $retour;
}

function highlight_code($code) {
    $code = str_replace("\n",' ', $code);
    $code = highlight_string('<?php '.$code.' ?>', true);
    $code = str_replace('&lt;?php&nbsp;','', $code);
    $code = str_replace('?&gt;','', $code);
    
    
    return $code;
}

function array2dot($points) {
    $retour = '';
    $subgraph = array();

    $occurrences = array();
    foreach($points as $origine => $destinations) {
        $occurrences[] = $origine;
        $occurrences = array_merge($occurrences, array_keys($destinations));
    }
    $occurrences = array_count_values($occurrences);
    
    foreach($points as $origine => $destinations) {
        // @todo : protéger les noms de fichiers
        $subgraph[dirname($origine)][] = $origine;
        foreach($destinations as $dest => $foo) {
            if ($occurrences[$dest] > 1) { 
                $retour .= "\"$origine\" -> \"$dest\";\n";
            }
            $subgraph[dirname($dest)][] = $dest;
        }
    }
    
    $dot = '';
    foreach($subgraph as $dir => $fichiers) {
        $fichiers = array_unique($fichiers);
        $fichiers2 = array();
        foreach($fichiers as $fichier) {
            if ($occurrences[$fichier] == 1) { continue; }
            $fichiers2[] = $fichier;
        }
        $dot .=  "subgraph \"cluster_$dir\" { label=\"$dir\"; \"".join('";"', $fichiers2)."\"; }\n";
    }
    $subgraph = $dot;
    
    $retour = "digraph G {
size=\"8,6\"; ratio=fill; node[fontsize=24];
$retour
$subgraph
}";

    return $retour;
}

    function print_query($requete) {
        print $this->prepare_query($requete)."\n";
        die();
    }

    function prepare_query($requete) {
        $requete = str_replace(array_keys($this->tables), array_values($this->tables), $requete);
        
        if (preg_match_all('/<\w+>/', $requete, $r)) {
            print "Il reste des tables à analyser : ".join(', ', $r[0]);
        }
        
        return $requete;
    }
    
    function exec_query($requete) {
        $requete = $this->prepare_query($requete);
        
        $res = $this->mid->query($requete);
        $erreur = $this->mid->errorInfo();
        
        if ($erreur[2]) {
            print_r($erreur);
            print $requete;
            die();
        }

        return $res;
    }
    
    function dependsOn() {
        return array();
    }
    
    function clean_rapport() {
        $requete = <<<SQL
DELETE FROM <rapport> WHERE module='{$this->name}'
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
DELETE FROM <rapport_dot> WHERE module='{$this->name}'
SQL;
        $this->exec_query($requete);

        $requete = <<<SQL
DELETE FROM <rapport_module> WHERE module='{$this->name}'
SQL;
        $this->exec_query($requete);
    }
    
    static public function getPHPFunctions() {
        // dépend du PHP d'exécution.
        // utiliser un .ini ou un fichier pour stocker cela
  	    $functions = get_defined_functions();
	    $extras = array('echo','print','die','exit','isset','empty','array','list','unset','eval');
	    return array_merge($functions['internal'], $extras);
    }
}
?>