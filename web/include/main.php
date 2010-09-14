<?php
    $query = "SELECT ML.module AS element, 
                     COUNT(RL.id) AS nb, 
                     COUNT(RL.id) - SUM(checked) AS todo 
                 FROM <rapport_module> ML
                 LEFT JOIN <rapport> RL
                    ON ML.module = RL.module
                 GROUP BY ML.module";
    $res = $DATABASE->query($query);

    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($rows as &$row) {
        $row['link'] = "index.php?module=".$row['element'];
        $row['element'] = $translations[$row['element']]['title'] ? $translations[$row['element']]['title'] : $row['element'];
    }
    
    usort($rows, 'cmp');
    
    print get_html_manual($rows);

    function cmp($a, $b) {
        if ($a['element'] == $b['element']) {
            return 0;
        }
        return ($a['element'] < $b['element']) ? -1 : 1;
    }
?>