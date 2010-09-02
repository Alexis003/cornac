<?php
    $requete = "SELECT element AS element, 
                       fichier AS fichier, 
                       1 AS nb,
                       id
                   FROM {$tables['<rapport>']} 
                   WHERE module='{$_GET['module']}' 
                   GROUP BY fichier, element";
    $res = $mysql->query($requete);
    
    $rows = array();
    while($row = $res->fetch()) {
        @$rows[$row['fichier']][] = $row; 
    }
        
    print get_html_level2($rows, $_GET['module']);
?>