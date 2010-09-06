<?php

// @todo : use the configuration file! 
$mysql = new pdo('mysql:dbname=analyseur;host=127.0.0.1','root','');

// @todo : use the configuration file!
$prefixe = 'affility';
    
// todo Export the table name creation to a new layer (common with the auditeur)
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

// @doc menu for the HTML display
$cas['html'] = array(
     'occurrences-element' => 'Occurrences',
     'occurrences-frequency' => 'Occurrences par fréquence',

     'files-occurrences' => 'Occurrences par fichier',
     'classes-occurrences' => 'Occurrences par classe',
     'methods-occurrences' => 'Occurrences par methode',
    
     'occurrences-fichiers' => 'Fichiers par occurrence',
     'occurrences-classes'  => 'Classes par occurrence',
     'occurrences-methods'  => 'Méthodes par occurrences',
);

$cas['dot'] = array('dot'  => 'format DOT',
                    'gexf' => 'format GEXF',
                    'json' => 'format JSON',
                    'text' => 'format Text',);

$entete = '';
foreach($cas[$format] as $titre => $c) {
    if (@$_GET['type'] == $titre) {
        $entete .= "<li><b>$c</b><br /> 
(<a href=\"index.php?module={$_GET['module']}&type=$titre&format=json\">json</a> - 
 <a href=\"index.php?module={$_GET['module']}&type=$titre&format=xml\">xml</a> - 
 <a href=\"index.php?module={$_GET['module']}&type=$titre&format=text\">text</a>)</li>";
    } else {
        $entete .= "<li><a href=\"index.php?module={$_GET['module']}&type=$titre\">$c</a></li>";
    }
}
$entete = "<table><tr><td><ul>$entete</ul></td>\n";
if (isset($translations[$_GET['module']]['title'])) {   
    $title = $translations[$_GET['module']]['title'];
    $description = $translations[$_GET['module']]['description'];
} else {
    $title = $_GET['module'].' (default)';
    $description = $_GET['module'];
}
$entete .= "<td><strong>{$title}</strong><br />{$description}</td></tr></table>\n";

if ($format == 'dot') {
    switch(@$_GET['type']) {
        case 'dot' : 
            $query = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
            $res = $mysql->query($query);
            $lignes = $res->fetchAll();
            include('format/dot.php');

            header('Content-type: application/dot');
            header('Content-Disposition: attachment; filename="'.$_GET['module'].'.dot"');
            print $dot;
            break;

        case 'gexf' : 
            $query = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
            $res = $mysql->query($query);
            $lignes = $res->fetchAll();
            include('format/gexf.php');

            header('Content-type: application/gexf');
            header('Content-Disposition: attachment; filename="'.$_GET['module'].'.gexf"');
            print $gexf;
            break;

        case 'json' : 
            $query = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
            $res = $mysql->query($query);
            $lignes = $res->fetchAll();
            
            header('Content-type: application/text');
            header('Content-Disposition: attachment; filename="'.$_GET['module'].'.json"');
            print json_encode($lignes);
            break;

        case 'text' : 
            $query = "SELECT a, b, cluster FROM {$tables['<rapport_dot>']} WHERE module='{$_GET['module']}'";
            $res = $mysql->query($query);
            $lignes = $res->fetchAll();
            
            header('Content-type: application/text');
            header('Content-Disposition: attachment; filename="'.$_GET['module'].'.txt"');
            print json_encode($lignes);
            break;

        default : 
            include('./format/html.php');
            print print_entete();
            print print_pieddepage();
    }
    die();
}

    
switch(@$_GET['type']) {
    case 'methods-occurrences' :
        $format = get_format();
        include("format/$format.php");
        include('include/methods_occurrences.php');
        break;

    case 'classes-occurrences' :
        $format = get_format();
        include("format/$format.php");
        include('include/classes_occurrences.php');
        break;

    case 'files-occurrences' :
        $format = get_format();
        include("format/$format.php");
        include('include/files_occurrences.php');
        break;

    case 'occurrences-methods' :
        $format = get_format();
        include("format/$format.php");
        include('include/occurrences_methods.php');
        break;

    case 'occurrences-classes' :
        $format = get_format();
        include("format/$format.php");
        include('include/occurrences_classes.php');
        break;

    case 'occurrences-fichiers' :
        $format = get_format();
        include("format/$format.php");
        include('include/occurrences_files.php');
        break;

    case 'occurrences-frequency' :
        $format = get_format();
        include("format/$format.php");
        include('include/occurrences_frequency.php');
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
        if (in_array($_GET['format'], array('json','text','html','xml'))) {
            return $_GET['format'];
        } else {
            return $default;
        }
    } elseif (isset($_POST['format'])) {
        if (in_array($_POST['format'], array('json','text','html','xml'))) {
            return $_POST['format'];
        } else {
            return $default;
        }
    } else {
        return $default;
    }
}
?>