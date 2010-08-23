<?php
        $requete = "
            SELECT concat(CR.fichier, ': <br /><b>', class,'->', scope,'</b>') as class, element, COUNT(*) AS nb 
            FROM {$tables['<rapport>']} CR
            JOIN {$tables['<tokens>']} T1
                ON CR.token_id = T1.id
                WHERE module='{$_GET['module']}' 
            GROUP BY concat(CR.fichier, class , scope), element";
        $res = $mysql->query($requete);

        $rows = array();
        while($row = $res->fetch()) {
            @$rows[$row['class']][] = $row;
        }

        print get_html_level2($rows);
?>