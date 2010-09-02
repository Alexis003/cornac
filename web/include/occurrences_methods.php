<?php
        $requete = <<<SQL
            SELECT CONCAT(if (class = '', 'global',class) ,'::', scope) AS element, 
                    element AS fichier, 
                    COUNT(*) AS nb,
                    COUNT(*) = SUM(checked) AS checked,
                    CR.id
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY CONCAT(class , scope), element
            ORDER BY CONCAT(if (class = '', 'global',class) ,'::', scope)
SQL;
        $res = $mysql->query($requete);

        $rows = array();
        while($row = $res->fetch()) {
            @$rows[$row['fichier']][] = $row;
        }

        print get_html_level2($rows, $_GET['module']);
?>