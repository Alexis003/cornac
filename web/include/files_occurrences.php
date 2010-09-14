<?php
    $requete = <<<SQL
SELECT element AS element, 
       fichier AS fichier, 
       COUNT(*) AS nb,
       id
FROM <rapport> TR 
WHERE TR.module='{$_CLEAN['module']}'
GROUP BY TR.fichier, TR.element
SQL;
    $res = $DATABASE->query($requete);
    
    $rows = array();
    while($row = $res->fetch(PDO::FETCH_ASSOC)) {
        @$rows[$row['fichier']][] = $row; 
    }
        
    print get_html_level2($rows, $_CLEAN['module']);
?>