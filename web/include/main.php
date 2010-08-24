<?php
    $query = "SELECT module as element, count(*) AS nb FROM {$tables['<rapport>']} GROUP BY module";
    $res = $mysql->query($query);

    $rows = $res->fetchAll();
    
    foreach($rows as &$row) {
        $row['link'] = "index.php?module=".$row['element'];
        $row['element'] = $translations[$row['element']]['title'] ? $translations[$row['element']]['title'] : $row['element'];
    }
    
    print get_html($rows);
?>