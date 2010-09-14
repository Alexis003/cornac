<?php
    $requete = "SELECT id, element, COUNT(*) AS nb,
                       COUNT(*) = SUM(checked) AS checked
    FROM <rapport> TR
    WHERE module='{$_CLEAN['module']}' 
        GROUP BY element 
        ORDER BY nb DESC";
    $res = $DATABASE->query($requete);
    $lines = $res->fetchAll(PDO::FETCH_ASSOC);
    
    print get_html_check($lines, $_CLEAN['module']);
?>