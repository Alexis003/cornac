<?php

function get_html($lines) {
        $total = count($lines);
        
        $html = '<table border="0" cellpadding="3" width="600">';
        $html .= '<tr><td class="h">Total</td><td class="h">'.$total.'</td></tr>';

        foreach($lines as $line) {
            $ligne['element'] = htmlentities($line['element']);
            $html .= '<tr><td class="e">'.$line['element'].'</td><td class="v">'.$line['nb'].'</td></tr>';
        }
        $html .= '<tr><td class="h">Total</td><td class="h">'.$total.'</td></tr>';
        $html .= "</table>";

        global $prefixe;
        $html = print_entete($prefixe).$html.print_pieddepage($prefixe);
        
        return $html;
}        


function get_html_level2($lines) {
        $total = count($lines);
        
        $html = '<table border="0" cellpadding="3" width="600">';
        $html .= '<tr><td class="e">Total</td><td class="v">'.$total.'</td></tr>';
        
        $id_tr = 0;
        foreach($lines as $file => $rows) {
            $id_tr++;
            $id_td = 0;
            $html .= '<tr id="tr_'.$id_tr.'" onClick="toggle_row(\'tr_'.$id_tr.'\', '.count($rows).');"><td class="h">'.$file.'</td><td class="h">'.count($rows).'</td></tr>';
            foreach($rows as $row) {
                $id_td++;
                $html .= '<tr id="tr_'.$id_tr.'_'.$id_td.'"  style="display: none"><td class="e">'.$row['element'].'</td><td class="v">'.$row['nb'].'</td></tr>';
            }
        }
        $html .= '<tr><td class="e">Total</td><td class="v">'.$total.'</td></tr>';
        $html .= "</table>";

        global $prefixe;
        $html = print_entete($prefixe).$html.print_pieddepage($prefixe);
        
        return $html;
}        

    
function print_entete($prefixe='Sans Nom') {
    global $entete;
    
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>Analyseur pour l'application $prefixe</title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
 <script type="text/javascript" src="site.js"></script>
 <style type="text/css" media="all">
  @import url("./css/site.css");
 </style>
</head>
<body>

<a href="index.php">Index</a> $entete
HTML;

}

function print_pieddepage($prefixe='Sans Nom') {
    return <<<HTML
    </body>
</html>
HTML;
}
?>