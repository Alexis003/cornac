<?php
    $requete = "SELECT id, element, COUNT(*) AS nb,
                       COUNT(*) = SUM(checked) AS checked
    FROM {$tables['<rapport>']} TR
    WHERE module='{$_GET['module']}' 
        GROUP BY element 
        ORDER BY element";
    $res = $mysql->query($requete);
    $lines = $res->fetchAll(PDO::FETCH_ASSOC);
    
    print get_html_check($lines, $_GET['module']);
?>