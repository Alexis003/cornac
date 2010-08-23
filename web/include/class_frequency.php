<?php
        $requete = "
            SELECT concat(CR.fichier, ': ', class) AS element, COUNT(*) AS nb 
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY concat(CR.fichier, ':', class), element ORDER BY nb DESC";
        $res = $mysql->query($requete);
        $lines = $res->fetchAll();
    
        print get_html($lines);
?>