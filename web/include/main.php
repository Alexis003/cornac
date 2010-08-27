<?php
    $query = "SELECT module as element, count(distinct element) AS nb FROM {$tables['<rapport>']} GROUP BY module";
    $res = $mysql->query($query);

    $rows = $res->fetchAll();
    
    foreach($rows as &$row) {
        $row['link'] = "index.php?module=".$row['element'];
        $row['element'] = $translations[$row['element']]['title'] ? $translations[$row['element']]['title'] : $row['element'];
    }
    
    usort($rows, 'cmp');
    
    print get_html($rows);

    function cmp($a, $b) {
        if ($a['element'] == $b['element']) {
            return 0;
        }
        return ($a['element'] < $b['element']) ? -1 : 1;
    }
?>