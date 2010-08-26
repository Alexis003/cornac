<?php

class Render_html {
    private $db = null;
    private $folder = '/tmp';
    
    function __construct($db, $module, $folders ) {
        $this->db = $db;
        $this->module = $module;

// @todo  : should be centralized somewhere !
        $prefixe = $this->module;
        $this->tables = array('<rapport>' => $prefixe.'_rapport',
                              '<rapport_scope>' => $prefixe.'_rapport_scope',
                              '<tokens>' => $prefixe.'',
                              '<tokens_tags>' => $prefixe.'_tags',
                              '<rapport_module>' => $prefixe.'_rapport_module',
                              '<rapport_dot>' => $prefixe.'_rapport_dot',
                            );

    }
    
    function SetFolder($path) {
        $this->folder = realpath($path);
    }
    
    function render($lines) {
        $query = "SELECT module FROM {$this->tables['<rapport_module>']} ORDER BY fait DESC ";
        $res = $this->db->query($query);

        $html = '';
        while($ligne = $res->fetch()) {
            $html .= "<li><a href=\"{$ligne['module']}.html\">{$ligne['module']}</a></li>";
            
            $this->render_index($ligne['module']);
            $this->render_occurrence_fichier($ligne['module']);
            $this->render_occurrences_freq($ligne['module']);
            $this->render_fichier_freq($ligne['module']);
            $this->render_scope_freq($ligne['module']);
            $this->render_classe_freq($ligne['module']);
        }
    
        $html = "<ul>
    $html
</ul>";
        $html = $this->entete().$html.$this->pieddepage();
        // @attention : missiong $folder
        file_put_contents($this->folder."/index.html", $html);        
    }

    function render_index($analyzer) {
        $query = "SELECT element, COUNT(*) AS nb FROM {$this->tables['<rapport>']} WHERE module='{$analyzer}' GROUP BY element ORDER BY element";
        $res = $this->db->query($query);
        
        $html = '';
        $total = 0;
        while($ligne = $res->fetch()) {
            $ligne['element'] = htmlentities($ligne['element']);
            $html .= "<li>{$ligne['element']} : {$ligne['nb']}</li>";
            $total++;
        }

        $html = $this->entete().
                $this->menu($analyzer, 'occurrence_fichier').
                "<p>Total : $total<br /></p>"."<ul>$html</ul>".
                $this->pieddepage();

        file_put_contents($this->folder."/".$analyzer.".html", $html);        
        file_put_contents($this->folder."/".$analyzer.".occurrences-fichier.html", $html);        
    }

    function render_classe_freq($analyzer) {
        $query = "SELECT fichier, element, COUNT(*) AS nb FROM {$this->tables['<rapport>']} WHERE module='{$analyzer}' GROUP BY fichier, element ORDER BY nb DESC";
        $res = $this->db->query($query);

        $html = '';
        $total = 0;
        $lignes = array();
        while($ligne = $res->fetch()) {
            $ligne['element'] = htmlentities($ligne['element']);
            @$lignes[$ligne['fichier']] .= "<li>{$ligne['element']} : {$ligne['nb']}</li>\n";
            $total++;
        }
        
        foreach($lignes as $fichier => $ligne) {
            $html .= "<li>$fichier (".(count(explode("\n", $ligne)) - 1).")<ul>$ligne</ul></li>";
        }

        $html = $this->entete().
                $this->menu($analyzer, 'occurrence_fichier').
                "<p>Total : $total<br /></p>"."<ul>$html</ul>".
                $this->pieddepage();

        file_put_contents($this->folder."/".$analyzer.".classe-freq.html", $html);        
    }

    function render_fichier_freq($analyzer) {
        $query = "SELECT fichier, element, COUNT(*) AS nb FROM {$this->tables['<rapport>']} WHERE module='{$analyzer}' GROUP BY fichier, element ORDER BY nb DESC";
        $res = $this->db->query($query);

        $html = '';
        $total = 0;
        $lignes = array();
        while($ligne = $res->fetch()) {
            $ligne['element'] = htmlentities($ligne['element']);
            @$lignes[$ligne['fichier']] .= "<li>{$ligne['element']} : {$ligne['nb']}</li>\n";
            $total++;
        }
        
        foreach($lignes as $fichier => $ligne) {
            $html .= "<li>$fichier (".(count(explode("\n", $ligne)) - 1).")<ul>$ligne</ul></li>";
        }

        $html = $this->entete().
                $this->menu($analyzer, 'occurrence_fichier').
                "<p>Total : $total<br /></p>"."<ul>$html</ul>".
                $this->pieddepage();

        file_put_contents($this->folder."/".$analyzer.".fichier-freq.html", $html);        
    }
    
    function render_occurrences_freq($analyzer) {
        $query = "SELECT element, COUNT(*) AS nb FROM {$this->tables['<rapport>']} WHERE module='{$analyzer}' GROUP BY element ORDER BY nb DESC";
        $res = $this->db->query($query);
        
        $html = '';
        $total = 0;
        while($ligne = $res->fetch()) {
            $ligne['element'] = htmlentities($ligne['element']);
            $html .= "<li>{$ligne['element']} : {$ligne['nb']}</li>";
            $total++;
        }

        $html = $this->entete().
                $this->menu($analyzer, 'occurrence_fichier').
                "<p>Entrées : ".$total."</p><ul>$html</ul>";
                $this->pieddepage();
        file_put_contents($this->folder."/".$analyzer.".occurrences-freq.html", $html);        
    }
    
    function render_occurrence_fichier($analyzer) {
        $query = "SELECT element, fichier FROM {$this->tables['<rapport>']} WHERE module='{$analyzer}' GROUP BY element, fichier";
        $res = $this->db->query($query);
        
        $html = '';
        $lignes = array();
        while($ligne = $res->fetch()) {
            @$lignes[$ligne['element']] .= "<li>{$ligne['fichier']}</li>\n";
        }
        
        $total = 0;
        foreach($lignes as $fichier => $ligne) {
            $html .= "<li>$fichier (".(count(explode("\n", $ligne)) - 1).")<ul>$ligne</ul></li>";
            $total++;
        }

        $html = $this->entete().
                $this->menu($analyzer, 'occurrence_fichier').
                "<p>Total : $total<br />Entrées : ".count($lignes)."</p><ul>$html</ul>".
                $this->pieddepage();
        file_put_contents($this->folder."/".$analyzer.".occurrence-fichier.html", $html);        
    }
    
    function render_scope_freq($analyzer) {
        $query = "
            SELECT concat(CR.fichier, ': <br /><b>', class,'->', scope,'</b>') as class, element, COUNT(*) AS nb 
            FROM {$this->tables['<rapport>']} CR
            JOIN {$this->tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$analyzer}' 
            GROUP BY concat(CR.fichier, class , scope), element";
        $res = $this->db->query($query);

        if ($res) {
            $html = '';
            $total = 0;
            $lignes = array();
            while($ligne = $res->fetch()) {
                $ligne['element'] = htmlentities($ligne['element']);
                @$lignes[$ligne['class']] .= "<li>{$ligne['element']} : {$ligne['nb']}</li>\n";
                $total++;
            }
        
            foreach($lignes as $class => $ligne) {
                $html .= "<li>$class (".(count(explode("\n", $ligne)) - 1).")<ul>$ligne</ul></li>";
            }
        } else {
            $html = '';
            $total = 0;
            $lignes = array();
        }

        $html = $this->entete().
                $this->menu($analyzer, 'occurrence_fichier').
                "<p>Total : $total<br />Entrées : ".count($lignes)."</p><ul>$html</ul>".
                $this->pieddepage();
        file_put_contents($this->folder."/".$analyzer.".scope-freq.html", $html);        
        return ;
    }
/*


$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

    $prefixe = 'tu';
    


if (!isset($_GET['module'])) {

    die();
}

    $query = "SELECT * FROM {$tables['<rapport_module>']} WHERE module=".$mysql->quote($_GET['module'])." ";
    $res = $mysql->query($query);
    
    $ligne = $res->fetch();
    $format = $ligne['format'];

    
    $entete = '';
    foreach($cas[$format] as $titre => $c) {
        if (@$_GET['type'] == $titre) {
            $entete .= " - <b>$c</b>";
        } else {
            $entete .= " - <a href=\"index.php?module={$_GET['module']}&type=$titre\">$c</a>";
        }
    }
    
switch(@$_GET['type']) {
    case 'gexf' : 

        $query = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
        $res = $mysql->query($query);

        $nodes = array();
        $edges = array();
        while($ligne = $res->fetch()) {
            if (($ida = in_array($ligne['a'], $nodes)) === false) {
                $nodes[] = $ligne['a'];
                $ida = count($nodes);
            }
            if (($idb = in_array($ligne['b'], $nodes)) === false) {
                $nodes[] = $ligne['b'];
                $idb = count($nodes);
            }
            
            $edges[] = "source=\"$ida\" target=\"$idb\"";
        }
        
        $liste_nodes = '';
        foreach($nodes as $id => $node) {
            $liste_nodes .= <<<XML
            <node id="$id" label="$node">
                <attvalues>
                </attvalues>
            </node>

XML;
        }
        
        $liste_edges = '';
        foreach($edges as $id => $node) {
            $liste_edges .= <<<XML
            <edge id="$id" $node />

XML;
        }

        $gexf = '<?xml version="1.0" encoding="UTF-8"?>';
        $gexf .= <<<XML

<gexf xmlns="http://www.gexf.net/1.1draft" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.gexf.net/1.1draft http://gexf.net/1.1draft.xsd" version="1.1">
    <meta lastmodifieddate="2009-03-20">
        <creator>Auditeur</creator>
        <description>{$_GET['module']}</description>
    </meta>
    <graph defaultedgetype="directed">
        <attributes class="node">
        <!--
            <attribute id="0" title="url" type="string"/>
            <attribute id="1" title="indegree" type="float"/>
            <attribute id="2" title="frog" type="boolean">
                <default>true</default>
            </attribute>
            -->
        </attributes>
        <nodes>
            $liste_nodes
        </nodes>
        <edges>
            $liste_edges
        </edges>
    </graph>
</gexf>    
XML;
        header('Content-type: application/gexf');
        header('Content-Disposition: attachment; filename="'.$_GET['module'].'.gexf"');
        print $gexf;
        break;

    case 'dot' :
        $query = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
        $res = $mysql->query($query);
        
        $dot =  "digraph G {
size=\"8,6\"; ratio=fill; node[fontsize=24];
";
        $clusters = array();
        while($ligne = $res->fetch()) {
            $dot .= "\"{$ligne['a']}\" -> \"{$ligne['b']}\";\n";
            if ($ligne['cluster']) {
                $clusters[$ligne['cluster']][] = $ligne['a'];
            }
        }
        
        if (count($clusters) > 0) {
          foreach($clusters as $nom => $liens) {
            $dot .= "subgraph \"cluster_$nom\" {label=\"$nom\"; \"".join('"; "', $liens)."\"; }\n";
          }
        }
        
        $dot .= '}';
        header('Content-type: application/dot');
        header('Content-Disposition: attachment; filename="'.$_GET['module'].'.dot"');
        print $dot;
        break;



}
*/
function entete($prefixe='Sans Nom') {
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>Analyseur pour l'application {$this->module}</title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body onload="boldEvents();">

<a href="index.html">Index</a>
HTML;

}

function pieddepage($prefixe='Sans Nom') {
    return <<<HTML

    </body>
</html>
HTML;

}    

function menu($analyzer, $type) {
    $cas = array('fichier-freq' => 'Frequence par fichier',
                 'classe-freq' => 'Frequence par classe',
                 'scope-freq' => 'Frequence par methode',
                 'occurrences-freq' => 'Occurrences, par fréquence',
                 'occurrences-element' => 'Occurrences, par ordre alphabetique',
                 'occurrence-fichier' => 'Liste des fichiers d\'apparition de chaque occurrence',
                 
                 );
/*
    $cas['dot'] = array('dot'  => 'format DOT',
                        'gexf' => 'format GEXF',);
                        */

    $entete = '';
    foreach($cas as $titre => $c) {
        if (@$type == $titre) {
            $entete .= " - <b>$c</b>";
        } else {
            $entete .= " - <a href=\"$analyzer.$titre.html\">$c</a>";
        }
    }
    return $entete;

}


}

?>