<?php

// @todo : centraliser les rquêtes SQL 
// @todo : mettre en parmètre 
abstract class modules {
    protected  $occurrences = 0;
    protected  $fichiers_identifies = 0;
    protected  $total_de_fichiers = 0;
    protected  $mid = null;
    
    const FORMAT_DEFAULT = 0;
    const FORMAT_HTMLLIST = 1;
    const FORMAT_DOT = 2;

    protected  $format = modules::FORMAT_HTMLLIST;

    function __construct($mid) {
        $this->mid = $mid;
        $this->format_export = modules::FORMAT_DEFAULT;
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
        
//        if (!isset($this->functions)) { print get_class($this)." n'a pas de functions\n";}
        
        $this->mid->query("REPLACE INTO rapport_module VALUES ('$this->name', NOW(), '{$this->format}')");
        print_r($this->mid->errorinfo());

/*
        if ($this->format_export == modules::FORMAT_DOT) {
            $this->export = $this->array2dot($this->functions);
        } else {
            $this->init_file();
            $this->export .= $this->array2li($this->functions);
            $this->finish_file();
        }

        $this->save_file($this->name);

        if (isset($this->inverse) && $this->inverse) {
            $this->init_file();
            $this->export = $this->array2li($this->array_invert($this->functions));
            $this->save_file($this->name.".inverse");        
        }
        */
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

    function updateCache() {
        return false;
        $requete = <<<SQL
SELECT tokens.id, tokens.droite, tokens.gauche, tokens.fichier FROM rapport 
    JOIN tokens ON rapport.token_id = tokens.id 
    LEFT JOIN caches ON rapport.id = caches.id  
WHERE rapport.module='{$this->name}'
SQL;
        $res = $this->mid->query($requete);
        
        include_once('classes/rendu.php');
        $rendu = new rendu($this->mid);

        while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $code = $rendu->rendu($ligne['droite'] , $ligne['gauche'] , $ligne['fichier']);
            
            $requete = <<<SQL
INSERT INTO caches VALUES ('{$ligne['id']}','$code');
SQL;
            $this->mid->query($requete);
        }
    }
}
?>