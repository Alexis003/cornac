<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
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

class Render_html {
    private $db = null;
    private $folder = '/tmp';
    
    function __construct($folders ) {
        global $DATABASE;

        $this->db = $DATABASE;
    }
    
    function SetFolder($path) {
        $this->folder = realpath($path);
    }
    
    function render($lines) {
        $query = "SELECT module FROM <rapport_module> ORDER BY fait DESC ";
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
        $query = "SELECT element, COUNT(*) AS nb FROM <rapport> WHERE module='{$analyzer}' GROUP BY element ORDER BY element";
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
        $query = "SELECT fichier, element, COUNT(*) AS nb FROM <rapport> WHERE module='{$analyzer}' GROUP BY file, element ORDER BY nb DESC";
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
        $query = "SELECT file, element, COUNT(*) AS nb FROM <rapport> WHERE module='{$analyzer}' GROUP BY file, element ORDER BY nb DESC";
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
        $query = "SELECT element, COUNT(*) AS nb FROM <rapport> WHERE module='{$analyzer}' GROUP BY element ORDER BY nb DESC";
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
        $query = "SELECT element, file FROM <rapport> WHERE module='{$analyzer}' GROUP BY element, file";
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
            SELECT concat(CR.file, ': <br /><b>', class,'->', scope,'</b>') as class, element, COUNT(*) AS nb 
            FROM <rapport> CR
            JOIN <tokens> T1
                ON CR.token_id = T1.id
                WHERE module='{$analyzer}' 
            GROUP BY concat(CR.file, class , scope), element";
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
                $this->menu($analyzer, 'occurrence_file').
                "<p>Total : $total<br />Entrées : ".count($lignes)."</p><ul>$html</ul>".
                $this->pieddepage();
        file_put_contents($this->folder."/".$analyzer.".scope-freq.html", $html);        
        return ;
    }

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