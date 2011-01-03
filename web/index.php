<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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
// @todo : use the configuration file! 
include('include/config.php');

// incoming variables
if (isset($_COOKIE['langue']) && in_array($_COOKIE['langue'], array('fr','en'))) {
    $translations = parse_ini_file('../dict/translations.'.$_COOKIE['langue'].'.ini', true);
} else {
    $translations = parse_ini_file('../dict/translations.en.ini', true);
    setcookie('langue','fr');
}

if (isset($_GET['type'])) {
    if (in_array($_GET['type'], array('dot',
                                      'gexf',
                                      'text',
                                      'json',
                                      'classes-occurrences' ,
                                      'files-occurrences' ,
                                      'methods-occurrences' ,
                                      'occurrences-classes' ,
                                      'occurrences-element' ,
                                      'occurrences-fichiers' ,
                                      'occurrences-frequency' ,
                                      'occurrences-methods' ))) { 
        $_CLEAN['type'] = $_GET['type'];
    } else {
        $_CLEAN['type'] = 'occurrences-element';
    }
} else {
    $_CLEAN['type'] = 'occurrences-element';
}

// incoming variables

// @todo : use the configuration file!
$prefix = $ini['cornac']['prefix'];

if (!isset($_GET['module'])) {
    $format = 'html';
    include("format/$format.php");
    include('include/main.php');
    die();
} else {
    $_CLEAN['module'] = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['module']);
}

if (isset($translations[$_CLEAN['module']]['title'])) {   
    $title = $translations[$_CLEAN['module']]['title'];
    $description = $translations[$_CLEAN['module']]['description'];
} else {
    $title = $_CLEAN['module'].' (default)';
    $description = $_CLEAN['module'];
}

$query_module = $DATABASE->quote($_CLEAN['module']);
$query = <<<SQL
SELECT * 
    FROM <rapport_module> 
    WHERE module=$query_module AND 
          web = 'yes'
SQL;
$res = $DATABASE->query($query);

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
    if ($_CLEAN['type'] == $titre) {
        $entete .= "<li><b>$c</b><br /> 
(<a href=\"index.php?module={$_CLEAN['module']}&type=$titre&format=json\">json</a> - 
 <a href=\"index.php?module={$_CLEAN['module']}&type=$titre&format=xml\">xml</a> - 
 <a href=\"index.php?module={$_CLEAN['module']}&type=$titre&format=text\">text</a> - 
 <a href=\"index.php?module={$_CLEAN['module']}&type=$titre&format=png\">png</a> - 
 )</li>";
    } else {
        $entete .= "<li><a href=\"index.php?module={$_CLEAN['module']}&type=$titre\">$c</a></li>";
    }
}

$entete = "<table><tr><td><ul>$entete</ul></td>\n";
$entete .= "<td><strong>{$title}</strong><br />{$description}</td></tr></table>\n";

if ($format == 'dot') {
    switch(@$_GET['type']) {
        case 'dot' : 
            $query = "SELECT a, b, cluster FROM <rapport_dot> WHERE module='{$_CLEAN['module']}'";
            $res = $DATABASE->query($query);
            $lignes = $res->fetchAll();
            include('format/dot.php');

            header('Content-type: application/dot');
            header('Content-Disposition: attachment; filename="'.$_CLEAN['module'].'.dot"');
            print $dot;
            break;

        case 'gexf' : 
            $query = "SELECT a, b, cluster FROM <rapport_dot> WHERE module='{$_CLEAN['module']}'";
            $res = $DATABASE->query($query);
            $lignes = $res->fetchAll();
            include('format/gexf.php');

            header('Content-type: application/gexf');
            header('Content-Disposition: attachment; filename="'.$_CLEAN['module'].'.gexf"');
            print $gexf;
            break;

        case 'json' : 
            $query = "SELECT a, b, cluster FROM <rapport_dot> WHERE module='{$_CLEAN['module']}'";
            $res = $DATABASE->query($query);
            $lignes = $res->fetchAll();
            
            header('Content-type: application/text');
            header('Content-Disposition: attachment; filename="'.$_CLEAN['module'].'.json"');
            print json_encode($lignes);
            break;

        case 'text' : 
            $query = "SELECT a, b, cluster FROM <rapport_dot> WHERE module='{$_CLEAN['module']}'";
            $res = $DATABASE->query($query);
            $lignes = $res->fetchAll();
            
            header('Content-type: application/text');
            header('Content-Disposition: attachment; filename="'.$_CLEAN['module'].'.txt"');
            print json_encode($lignes);
            break;

        default : 
            include('./format/html.php');
            print print_entete();
            print print_pieddepage();
    }
    die();
}

    
switch($_CLEAN['type']) {
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
        if (in_array($_GET['format'], array('json','text','html','xml','png'))) {
            return $_GET['format'];
        } else {
            return $default;
        }
    } elseif (isset($_POST['format'])) {
        if (in_array($_POST['format'], array('json','text','html','xml','png'))) {
            return $_POST['format'];
        } else {
            return $default;
        }
    } else {
        return $default;
    }
}
?>