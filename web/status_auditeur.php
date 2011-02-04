<html>                                                                  
 <head>                                                                  
 <script type="text/javascript" src="js/jquery.min.js"></script>          
 </head>                                                                 
 <body>   
<?php

include('include/config.php');

$stats = array();

$res = $DATABASE->query('SELECT COUNT(*) AS count
                                FROM <tasks> 
                                WHERE task="auditeur" AND
                                      completed=0');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$stats['Ready to be processed'] = $rows[0]['count'];

$res = $DATABASE->query('SELECT COUNT(*) AS count
                                FROM <tasks> 
                                WHERE task="auditeur" AND
                                      completed=1');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$stats['Running'] = $rows[0]['count'];

$res = $DATABASE->query('SELECT SUM(completed) / COUNT(*) AS progress,
                                COUNT(*) AS count
                                FROM <tasks> WHERE task="auditeur"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$stats['Ran'] = $rows[0]['count'];

$res = $DATABASE->query('SELECT * FROM <tasks> WHERE task="auditeur"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$groups = glob('../auditeur/classes/*.php');

include('../auditeur/classes/abstract/modules.php');
include('../auditeur/classes/abstract/modules_head.php');
foreach($groups as $group) {
    include_once($group);
    $class = substr(basename($group,'php'), 0, -1);
    
    $object = new $class(null);
    $dependencies = $object->dependsOn();
    
    $done = 0;
    foreach($rows as $row) {
        if (in_array($row['target'], $dependencies)) {
            $done++;
        }
    }
    
    print "$class\n";
    print count($dependencies)." count\n";
    print $done."\n\n";
    $rows[] = array('target' => $class, 
                    'completed' => number_format($done * 100 / count($dependencies), 0));
}

$groups = glob('../auditeur/classes/*/*.php');
foreach($groups as $id => &$group) {
    if (strpos($group, 'abstract') !== false) { 
        unset($groups[$id]); 
        continue; 
    }
    $group = str_replace('../auditeur/classes/','', $group);
    $group = str_replace('.php','', $group);
    $group = str_replace('/','_', $group);
}

foreach($rows as $row) {
    if ($x = array_search($row['target'], $groups)) {
       $rows[] = array('target' => $row['target'], 
                       'completed' => -1);

        unset($groups[$x]);
    }
}

$stats['Total analyzers'] = count($rows);

$html .= "<table>\n";
foreach($stats as $name => $value) {
    $html .= "<tr>
  <td>$name</td>
  <td>$value</td>
</tr>
";
}

$html .= "</table>\n<hr />\n";

$html .= "<table>
<tr>
  <td>File</td>
  <td>Progress</td>
</tr>
";
foreach($rows as $id => $row) {
    $result = $row['completed'];
/*
    switch ($row['completed']) {
        case 0: 
            $result = '^';
            break;
        
        case 1: 
            $result = '...';
            break;
        
        case 2: 
            $result = '<?';
            break;
            
        case 3: 
            $result = 'X';
            break;
            
        case 100 : 
            $result = 'OK';
            break;
        
        default : 
            $result = '??';
    }
    */
    $target_urlencoded = urlencode($row['target']);
    $html .= <<<HTML
<tr id="table_$id">
  <td>{$row['target']}</td>
  <td id="table_{$id}_2">$result %</td>
  <td><input type=button value="redo" onClick="var xhReq = new XMLHttpRequest();
 xhReq.open('GET', 'rebuild_auditeur.php?analyzer={$row['target']}', false);
 xhReq.send(null);
 $('#table_{$id}_2').html('0 %');"></td>
</tr>
HTML;
}
$html .= "
</table>";
print $html;

?></body>                                                                 
 </html>