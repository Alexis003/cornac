<?php
    $requete = "SELECT fichier, element, COUNT(*) AS nb FROM {$tables['<rapport>']} WHERE module='{$_GET['module']}' GROUP BY fichier, element ORDER BY nb DESC";
    $res = $mysql->query($requete);
    
    $rows = array();
    while($row = $res->fetch()) {
        @$rows[$row['fichier']][] = $row; 
    }
    
    print get_html_level2($rows);
?>