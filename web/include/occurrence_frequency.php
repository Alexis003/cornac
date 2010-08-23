<?php
    $requete = "SELECT element, COUNT(*) AS nb FROM {$tables['<rapport>']} WHERE module='{$_GET['module']}' GROUP BY element ORDER BY nb DESC";
    $res = $mysql->query($requete);
    $lines = $res->fetchAll();
    
    print get_html($lines);
?>