<?php

$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');
$prefixe = 'dotclear';
    
$tables = array('<rapport>' => $prefixe.'_rapport',
                '<rapport_scope>' => $prefixe.'_rapport_scope',
                '<tokens>' => $prefixe.'',
                '<tokens_tags>' => $prefixe.'_tags',
                '<rapport_module>' => $prefixe.'_rapport_module',
                '<rapport_dot>' => $prefixe.'_rapport_dot',
                );

if (isset($_COOKIE['langue']) && in_array($_COOKIE['langue'], array('fr','en'))) {
    $translations = parse_ini_file('../dict/translations.'.$_COOKIE['langue'].'.ini', true);
} else {
    $translations = parse_ini_file('../dict/translations.en.ini', true);
    setcookie('langue','fr');
}

if (!isset($_GET['module'])) {
    $format = 'html';
    include("format/$format.php");
    include('include/main.php');
    die();
}

    $requete = "SELECT * FROM {$tables['<rapport_module>']} WHERE module=".$mysql->quote($_GET['module'])." ";
    $res = $mysql->query($requete);
    
    $ligne = $res->fetch();
    $format = $ligne['format'];
    if (empty($format)) {
        header('Location: index.php');
        die();
    }

    $cas['html'] = array('fichier-freq' => 'Frequence par fichier',
                 'classe-freq' => 'Frequence par classe',
                 'scope-freq' => 'Frequence par methode',
                 'occurrences-freq' => 'Occurrences, par fréquence',
                 'occurrences-element' => 'Occurrences, par ordre alphabetique',
                 'occurrence-fichier' => 'Liste des fichiers d\'apparition de chaque occurrence'
                 
                 );
    $cas['dot'] = array('dot'  => 'format DOT',
                        'gexf' => 'format GEXF',
                        'json' => 'format JSON',);
    
    $entete = '';
    foreach($cas[$format] as $titre => $c) {
        if (@$_GET['type'] == $titre) {
            $entete .= "<li><b>$c</b> (<a href=\"index.php?module={$_GET['module']}&type=$titre&format=json\">json</a> - <a href=\"index.php?module={$_GET['module']}&type=$titre&format=xml\">xml</a>)</li>";
        } else {
            $entete .= "<li><a href=\"index.php?module={$_GET['module']}&type=$titre\">$c</a></li>";
        }
    }
    $entete = "<table   ><tr><td><ul>$entete</ul></td>\n";
    $entete .= "<td><strong>{$translations[$_GET['module']]['title']}</strong><br />{$translations[$_GET['module']]['description']}</td></tr></table>\n";

if ($format == 'dot' && !isset($cas['dot'][@$_GET['type']])) {
        print_entete($prefixe);
        print $entete;

        print_pieddepage($prefixe);
        die();
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
        $format = get_format();
        include("format/$format.php");
        include('include/file_occurrences.php');
        break;
        
    case 'fichier-freq' :
        $format = get_format();
        include("format/$format.php");
        include('include/file_frequency.php');
        break;

    case 'scope-freq' :
        $format = get_format();
        include("format/$format.php");
        include('include/scope_frequency.php');
        break;

    case 'classe-freq' :
        $format = get_format();
        include("format/$format.php");
        include('include/class_frequency.php');
        break;
        
    case 'occurrences-freq' :
        $format = get_format();
        include("format/$format.php");
        include('include/occurrence_frequency.php');
        break;

    case 'occurrences-element' :
    default : 
        $format = get_format();
        include("format/$format.php");
        include('include/default.php');
        break;
}

function get_format($default = 'html') {
    if (isset($_GET['format'])) {
        if (in_array($_GET['format'], array('json','html','xml'))) {
            return $_GET['format'];
        } else {
            return $default;
        }
    } elseif (isset($_POST['format'])) {
        if (in_array($_POST['format'], array('json','html','xml'))) {
            return $_POST['format'];
        } else {
            return $default;
        }
    } else {
        return $default;
    }
}
?>