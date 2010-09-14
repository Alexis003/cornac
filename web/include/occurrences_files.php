<?php
    $requete = "SELECT element AS fichier, 
                       fichier AS element, 
                       COUNT(*) AS nb,
                       id
                   FROM <rapport> 
                   WHERE module='{$_CLEAN['module']}'
                   GROUP BY element, fichier";
    $res = $DATABASE->query($requete);
    
    $rows = array();
    while($row = $res->fetch(PDO::FETCH_ASSOC)) {
        @$rows[$row['fichier']][] = $row; 
    }
        
    print get_html_level2($rows, $_CLEAN['module']);
?>