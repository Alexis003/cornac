<html>                                                                  
 <head>                                                                  
 <script type="text/javascript" src="js/jquery.min.js"></script>          
 <script type="text/javascript">                                         
$(document).ready(function() {
   $("tr > td:contains('OK')").css("background-color","green");
   $("tr > td:contains('X')").css("background-color","red");
   $("tr > td:contains('^')").css("background-color","blue");
 });
 </script>                                                               
 </head>                                                                 
 <body>   
<?php

include('include/config.php');

$res = $DATABASE->query('SELECT SUM(completed) / COUNT(*) AS progress,
                                COUNT(*) AS count 
                                FROM <tasks> WHERE task="tokenize"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$progress = number_format($rows[0]['progress'], 2);
$count = $rows[0]['count'];

$res = $DATABASE->query('SELECT * FROM <tasks> WHERE task="tokenize"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$html .= "<table>
<tr>
  <td>File</td>
  <td>Progress</td>
</tr>
<tr>
  <td>$count</td>
  <td>$progress %</td>
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
    $html .= <<<HTML
<tr>
  <td>{$row['target']}</td>
  <td>$result</td>
</tr>
HTML;
}
$html .= "<tr>
  <td>$count</td>
  <td>$progress %</td>
</tr>
</table>";
print $html;


$res = $DATABASE->query('SELECT SUM(completed) / COUNT(*) AS progress,
                                COUNT(*) AS count 
                                FROM <tasks> WHERE task="auditeur"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
$progress = number_format($rows[0]['progress'], 2);
$count = $rows[0]['count'];

$res = $DATABASE->query('SELECT * FROM <tasks> WHERE task="auditeur"');
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$html = '';

$html .= "<table>
<tr>
  <td>File</td>
  <td>Progress</td>
</tr>
<tr>
  <td>$count</td>
  <td>$progress %</td>
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
    $html .= <<<HTML
<tr>
  <td>{$row['target']}</td>
  <td>$result</td>
</tr>
HTML;
}
$html .= "<tr>
  <td>$count</td>
  <td>$progress %</td>
</tr>
</table>";
print $html;

?></body>                                                                 
 </html>