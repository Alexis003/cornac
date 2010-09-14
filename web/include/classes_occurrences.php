<?php
        $requete = <<<SQL
            SELECT 
                IF (class = '', 'global',class) AS fichier, 
                element AS element, 
                COUNT(*) AS nb,
                TR.id,
                COUNT(*) = SUM(checked) AS checked
            FROM <rapport> TR
            JOIN <tokens> T1
                ON TR.token_id = T1.id
                WHERE TR.module='{$_CLEAN['module']}' 
            GROUP BY T1.class, TR.element
            ORDER BY if (class = '', 'global', T1.class) 
SQL;
        $res = $DATABASE->query($requete);

        $rows = array();
        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
            @$rows[$row['fichier']][] = $row;
        }

        print get_html_level2($rows, $_CLEAN['module']);
?>