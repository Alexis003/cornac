<?php
    $requete = "SELECT element AS fichier, 
                       fichier AS element, 
                       1 AS nb,
                       id
                   FROM {$tables['<rapport>']} 
                   WHERE module='{$_GET['module']}' 
                   GROUP BY element, fichier";
    $res = $mysql->query($requete);
    
    $rows = array();
    while($row = $res->fetch()) {
        @$rows[$row['fichier']][] = $row; 
    }
        
    print get_html_level2($rows);
?>