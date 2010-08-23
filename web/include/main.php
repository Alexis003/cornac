<?php
    $query = "SELECT module as element, count(*) AS nb FROM {$tables['<rapport>']} GROUP BY module";
    $res = $mysql->query($query);

    $rows = $res->fetchAll();
    
    foreach($rows as &$row) {
        $row['element'] = "<a href=\"index.php?module=".$row['element']."\">".$row['element']."</a>";
    }
    
    print get_html($rows);
?>