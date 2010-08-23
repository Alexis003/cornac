<?php
        $requete = <<< SQL
            SELECT if(class='', 'global',class) AS element, COUNT(*) AS nb 
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY class
            ORDER BY nb DESC
SQL;
        $res = $mysql->query($requete);
        $lines = $res->fetchAll();
    
        print get_html($lines);
?>