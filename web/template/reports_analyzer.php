<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>Cornac analysis for this project : report</title>
</head>
<body>
<?php

print "<h1>$analyzer</h1>";

echo '<a href="'.$url_main.'">Main</a> - <a href="'.$url_reports.'">Reports</a> - <a href="'.$url_report_file.'">Reports by file</a>';
// @todo make this work again later
// echo ' - <a href="actions/reports_get_previous.php?analyzer='.$analyzer.'">Previous Analyzer</a> - <a href="actions/reports_get_next.php?analyzer='.$analyzer.'">Next Analyzer</a> ';
$html = '';

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
  <td>{$row['element']}</td>
  <td>{$row['file']}</td>
  <td>{$row['line']}</td>
</tr>
";
}

$html .= "</table>\n";

print $html;

?>
</body>
</html>