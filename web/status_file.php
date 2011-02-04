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
                                WHERE task="tokenize" AND
                                      completed=3');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$stats['Number of uncompilable files'] = $rows[0]['count'];

$res = $DATABASE->query('SELECT COUNT(*) AS count
                                FROM <tasks> 
                                WHERE task="tokenize" AND
                                      completed=0');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$stats['Number of waiting files'] = $rows[0]['count'];

$res = $DATABASE->query('SELECT COUNT(*) AS count
                                FROM <tasks> 
                                WHERE task="tokenize" AND
                                      completed=2');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$stats['Number of in process files'] = $rows[0]['count'];

$res = $DATABASE->query('SELECT SUM(completed) / COUNT(*) AS progress,
                                COUNT(*) AS count
                                FROM <tasks> WHERE task="tokenize"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$progress = number_format($rows[0]['progress'], 2);
$stats['Number of files'] = $rows[0]['count'];
$stats['Number of processed files'] = $rows[0]['count'] - $stats['Number of uncompilable files'] - $stats['Number of in process files'] - $stats['Number of waiting files'];

$res = $DATABASE->query('SELECT * FROM <tasks> WHERE task="tokenize"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

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
foreach($rows as $row) {
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
    $target_urlencoded = urlencode($row['target']);
    $html .= <<<HTML
<tr>
  <td>{$row['target']}</td>
  <td><a href="rebuild.php?file=$target_urlencoded">$result</a></td>
</tr>
HTML;
}
$html .= "
</table>";
print $html;

?></body>                                                                 
 </html>