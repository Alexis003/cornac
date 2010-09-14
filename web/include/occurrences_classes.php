<?php
        $requete = <<<SQL
            SELECT 
                IF (class = '', 'global',class) AS element, 
                element AS fichier, 
                COUNT(*) AS nb,
                CR.id,
                COUNT(*) = SUM(checked) AS checked
            FROM <rapport> CR
            JOIN <tokens> T1
                ON CR.token_id = T1.id
                WHERE module='{$_CLEAN['module']}' 
            GROUP BY element, class
            ORDER BY if (class = '', 'global',class) 
SQL;
        $res = $DATABASE->query($requete);

        $rows = array();
        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
            @$rows[$row['fichier']][] = $row;
        }

        print get_html_level2($rows, $_CLEAN['module']);
?>