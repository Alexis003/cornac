<?php
        $requete = <<<SQL
            SELECT 
                IF (class = '', 'global',class) AS element, 
                element AS fichier, 
                COUNT(*) AS nb,
                CR.id,
                COUNT(*) = SUM(checked) AS checked
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY element, class
            ORDER BY if (class = '', 'global',class) 
SQL;
        $res = $mysql->query($requete);

        $rows = array();
        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
            @$rows[$row['fichier']][] = $row;
        }

        print get_html_level2($rows, $_GET['module']);
?>