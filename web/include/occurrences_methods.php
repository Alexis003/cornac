<?php
$requete = <<<SQL
    SELECT CONCAT(if (class = '', 'global',class) ,'::', scope) AS element, 
            element AS fichier, 
            COUNT(*) AS nb,
            COUNT(*) = SUM(checked) AS checked,
            CR.id
    FROM <rapport> CR
    JOIN <tokens> T1
        ON CR.token_id = T1.id
        WHERE module='{$_CLEAN['module']}' 
    GROUP BY CONCAT(class , scope), element
    ORDER BY CONCAT(if (class = '', 'global',class) ,'::', scope)
SQL;
        $res = $DATABASE->query($requete);

        $rows = array();
        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
            @$rows[$row['fichier']][] = $row;
        }

        print get_html_level2($rows, $_CLEAN['module']);
?>