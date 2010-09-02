<?php
        $requete = <<<SQL
            SELECT 
                IF (class = '', 'global',class) AS fichier, 
                element AS element, 
                COUNT(*) AS nb,
                CR.id,
                COUNT(*) = SUM(checked) AS checked
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY class, element
            ORDER BY if (class = '', 'global',class) 
SQL;
        $res = $mysql->query($requete);

        $rows = array();
        while($row = $res->fetch()) {
            @$rows[$row['fichier']][] = $row;
        }

        print get_html_level2($rows, $_GET['module']);
?>