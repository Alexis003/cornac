<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>Cornac analysis for this project : list of all reports</title>
</head>
<body>
<a href="index.php">Main</a>
<?php

include('include/config.php');

$html = '';
$stats = array();

$sql = <<<SQL
SELECT TRM.module, TRM.fait, COUNT(*) AS count
FROM <report_module> TRM
JOIN <report> TR
    ON TR.module = TRM.module
GROUP BY TR.module
ORDER BY TR.module
SQL;
$res = $DATABASE->query($sql);
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$html .= "<table>\n";
foreach($rows as $id => $row) {
    $html .= "<tr>
  <td><a href=\"reports_analyzer.php?analyzer={$row['module']}\">{$row['module']}</a></td>
  <td>{$row['fait']}</td>
  <td style=\"text-align: right\">{$row['count']}</td>
</tr>
";
}

$html .= "</table>\n";

print $html;

?>
</body>
</html>