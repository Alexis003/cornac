<?php
        $requete = <<<SQL
            SELECT if (class = '', 'global',class) AS class, element, COUNT(*) AS nb 
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
            @$rows[$row['class']][] = $row;
        }

        print get_html_level2($rows);
?>