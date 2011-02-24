<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>Cornac analysis for this project : Tokenizeur report</title>
</head>
<body>
<?php

include('include/config.php');

// @todo validate this! 
$analyzer = $_GET['analyzer'];

print "<h1>$analyzer</h1>";
echo '<a href="index.php">Main</a> - <a href="reports.php">Reports</a> - <a href="reports_analyzer.php?analyzer='.$analyzer.'">Reports by file</a>';

$html = '';
$stats = array();

// @todo this is ugly! Make this better soon!
$sql = <<<SQL
SELECT DISTINCT TR.file
FROM <report> TR
JOIN <tokens> T1
    ON TR.token_id = T1.id
WHERE module='$analyzer'
ORDER BY element, TR.file, line
SQL;
$res = $DATABASE->query($sql);
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$stats['distinct'] = count($rows);

$sql = <<<SQL
SELECT TR.element, TR.file, T1.line
FROM <report> TR
JOIN <tokens> T1
    ON TR.token_id = T1.id
WHERE module='$analyzer'
ORDER BY file, line, element
SQL;
$res = $DATABASE->query($sql);
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$stats['total'] = count($rows);

$html .= "<table>\n";
foreach($stats as $name => $value) {
    $html .= "<tr>
  <td>{$name}</td>
  <td>{$value}</td>
</tr>
";
}
$html .= "</table>\n";

$html .= "<table>\n";
foreach($rows as $id => $row) {
    $html .= "<tr>
  <td>{$row['file']}</td>
  <td>{$row['line']}</td>
  <td>{$row['element']}</td>
</tr>
";
}

$html .= "</table>\n";

print $html;

?>
</body>
</html>