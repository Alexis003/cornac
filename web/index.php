<?php

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

    $prefixe = 'tu';
    
        $tables = array('<rapport>' => $prefixe.'_rapport',
                        '<rapport_scope>' => $prefixe.'_rapport_scope',
                              '<tokens>' => $prefixe.'',
                              '<tokens_tags>' => $prefixe.'_tags',
                              '<rapport_module>' => $prefixe.'_rapport_module',
                              '<rapport_dot>' => $prefixe.'_rapport_dot',
                            );


if (!isset($_GET['module'])) {
    $requete = "SELECT module FROM {$tables['<rapport_module>']} ORDER BY fait DESC ";
    $res = $mysql->query($requete);

    $html = '';
    while($ligne = $res->fetch()) {
        $html .= "<li><a href=\"index.php?module={$ligne['module']}\">{$ligne['module']}</a></li>";
    }
    
    print_entete($prefixe);
    print "<ul>
    $html
</ul>";
    print_pieddepage($prefixe);
    die();
}

    $requete = "SELECT * FROM {$tables['<rapport_module>']} WHERE module=".$mysql->quote($_GET['module'])." ";
    $res = $mysql->query($requete);
    
    $ligne = $res->fetch();
    $format = $ligne['format'];

    $cas['html'] = array('fichier-freq' => 'Frequence par fichier',
                 'classe-freq' => 'Frequence par classe',
                 'scope-freq' => 'Frequence par methode',
                 'occurrences-freq' => 'Occurrences, par fréquence',
                 'occurrences-element' => 'Occurrences, par ordre alphabetique',
                 'occurrence-fichier' => 'Liste des fichiers d\'apparition de chaque occurrence',
                 
                 );
    $cas['dot'] = array('dot'  => 'format DOT',
                        'gexf' => 'format GEXF',);
    
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

        $requete = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
        $res = $mysql->query($requete);

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
        /*
                    <node id="0" label="Gephi">
                <attvalues>
                    <attvalue for="0" value="http://gephi.org"/>
                    <attvalue for="1" value="1"/>
                </attvalues>
            </node>

        */
        
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
        $requete = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
        $res = $mysql->query($requete);
        
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

    case 'occurrence-fichier' :
        $requete = "SELECT element, fichier FROM {$tables['<rapport>']} WHERE module='{$_GET['module']}' GROUP BY element, fichier";
        $res = $mysql->query($requete);
        
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

        print_entete($prefixe);
        print $entete;
        print "<p>Total : $total<br />Entrées : ".count($lignes)."</p><ul>$html</ul>";
        print_pieddepage($prefixe);
        break;
        
    case 'fichier-freq' :
        $requete = "SELECT fichier, element, COUNT(*) AS nb FROM {$tables['<rapport>']} WHERE module='{$_GET['module']}' GROUP BY fichier, element ORDER BY nb DESC";
        $res = $mysql->query($requete);

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

        print_entete($prefixe);
        print $entete;
        print "<p>Total : $total<br />Entrées : ".count($lignes)."</p><ul>$html</ul>";
        print_pieddepage($prefixe);
        break;

    case 'scope-freq' :
        $requete = "
            SELECT concat(CR.fichier, ': <br /><b>', class,'->', scope,'</b>') as class, element, COUNT(*) AS nb 
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY concat(CR.fichier, class , scope), element";
        $res = $mysql->query($requete);

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

        print_entete($prefixe);
        print $entete;
        print "<p>Total : $total<br />Entrées : ".count($lignes)."</p><ul>$html</ul>";
        print_pieddepage($prefixe);
        break;

    case 'classe-freq' :
        $requete = "
            SELECT concat(CR.fichier, ': ', class) as class, element, COUNT(*) AS nb 
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY concat(CR.fichier, ':', class), element ORDER BY nb DESC";
        $res = $mysql->query($requete);

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

        print_entete($prefixe);
        print $entete;
        print "<p>Total : $total<br />Entrées : ".count($lignes)."</p><ul>$html</ul>";
        print_pieddepage($prefixe);
        break;
    case 'occurrences-freq' :
        $requete = "SELECT element, COUNT(*) AS nb FROM {$tables['<rapport>']} WHERE module='{$_GET['module']}' GROUP BY element ORDER BY nb DESC";
        $res = $mysql->query($requete);
        
        $html = '';
        $total = 0;
        while($ligne = $res->fetch()) {
            $ligne['element'] = htmlentities($ligne['element']);
            $html .= "<li>{$ligne['element']} : {$ligne['nb']}</li>";
            $total++;
        }
        
        print_entete($prefixe);
        print $entete;
        print "<p>Entrées : ".$total."</p><ul>$html</ul>";
        print_pieddepage($prefixe);
        break;

    case 'occurrences-element' :
    default : 

        $requete = "SELECT element, COUNT(*) AS nb FROM {$tables['<rapport>']} WHERE module='{$_GET['module']}' GROUP BY element ORDER BY element";
        $res = $mysql->query($requete);
        
        $html = '';
        $total = 0;
        while($ligne = $res->fetch()) {
            $ligne['element'] = htmlentities($ligne['element']);
            $html .= "<li>{$ligne['element']} : {$ligne['nb']}</li>";
            $total++;
        }
        
        print_entete($prefixe);
        print $entete;
        print "<p>Total : $total<br /></p>";
        print "<ul>$html</ul>";
        print_pieddepage($prefixe);
}

function print_entete($prefixe='Sans Nom') {
    print <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>Analyseur pour l'application $prefixe</title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body onload="boldEvents();">

<a href="index.php">Index</a>
HTML;

}

function print_pieddepage($prefixe='Sans Nom') {
    print <<<HTML

    </body>
</html>
HTML;

    
}
?>