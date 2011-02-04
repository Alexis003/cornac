<html>                                                                  
 <head>                                                                  
 <script type="text/javascript" src="js/jquery.min.js"></script>          

 </head>                                                                 
 <body>   
<?php

include('include/config.php');
include('../libs/write_ini_file.php');

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

$html .= "<table>\n";
foreach($stats as $name => $value) {
    $html .= "<tr>
  <td>$name</td>
  <td>$value</td>
</tr>
";
}

$html .= "</table>\n<hr />\n";
$html = "";

//$res = $DATABASE->query('SELECT CONCAT(module," (", COUNT(*),")") AS module FROM <report> GROUP BY MODULE');
$res = $DATABASE->query('SELECT module AS module FROM <report> GROUP BY module');
$modules = multi2array($res->fetchAll(PDO::FETCH_ASSOC));
array_unshift($modules, 'any');

$res = $DATABASE->query('SELECT DISTINCT file FROM <report>');
$files = multi2array($res->fetchAll(PDO::FETCH_ASSOC));
array_unshift($files, 'any');

$where = array();

if (!isset($_GET['module']) || !in_array($_GET['module'], $modules)) {
    $module = 'any';
} else {
    $module = $_GET['module'];
    if ($module != 'any') {
        $where[] =  'module = '.$DATABASE->quote($_GET['module']).'';
    }
}

if (!isset($_GET['file']) || !in_array($_GET['file'], $files)) {
    $file = 'any';
} else {
    $file = $_GET['file'];
    if ($file != 'any') {
        $where[] =  'file = '.$DATABASE->quote($_GET['file']).'';
    }
}

$query = 'SELECT * FROM <report> ';
if (count($where) > 0) {
    $query .= ' WHERE '.join(' AND ', $where);
}
$query .=  ' LIMIT 100';

$res = $DATABASE->query($query);
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$module_select = array2li($modules, 'module', $module);
$file_select = array2li($files, 'file', $file);

// @todo make the 'Module' and 'File' into a drop down menu
$html .= "<table>
<tr>
  <td>id</td>
  <td>$file_select</td>
  <td>Element</td>
  <td>Token_id</td>
  <td>$module_select</td>
  <td>Checked</td>
</tr>
";
foreach($rows as $row) {
    $tds = "  <td>\n".join("</td>\n  <td>\n", $row)."</td>\n";
    $html .= <<<HTML
<tr>
$tds
</tr>
HTML;
}
$html .= "
</table>";
print $html;

function array2li($array, $name = "no_name", $selected = null) {
    $select = "<select name=\"$name\" id=\"$name\">\n";
    $select .= "  <option>".join("</option>\n  <option>", $array)."</option>\n";
    if (!is_null($selected)) {
        $select = str_replace('<option>'.$selected.'</option>', '<option selected="selected">'.$selected.'</option>', $select);
    }
    $select .= "</select>\n";

    return $select;
}

?>
 <script type="text/javascript">
$("#file").change(function() {
    var file = $("#file option:selected");
    var module = $("#module option:selected");
    
    document.location.replace('status_module.php?module='+ module.text() + '&file=' + file.text());
});
$("#module").change(function() {
    var file = $("#file option:selected");
    var module = $("#module option:selected");

    document.location.replace('status_module.php?module='+ module.text() + '&file=' + file.text());
});

 </script>          

</body>                                                                 
 </html>