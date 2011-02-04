<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */
// @todo : use the configuration file! 
include('include/config.php');
include('format/html.php');

print_r($_POST);

print print_entete();

// @todo LIMIT must be in config
if (isset($_GET['update'])) {
    $res = $DATABASE->query("SELECT count(*) FROM cnc_daily 
    WHERE date_report = CURDATE()");
    $row = $res->fetch();
    
    
    if ($row[0] == 0) {
        print "<p>MIse à jour</p>";
        $DATABASE->query("
CREATE TEMPORARY TABLE tmp_daily
SELECT TR.id, TR.module
FROM cnc_rapport TR
WHERE TR.module IN ('variables_one_letter','dieexit','unused_args') AND
checked = 0
ORDER BY RAND()
LIMIT 12
");

        $DATABASE->query("
INSERT INTO cnc_daily
SELECT NULL, 1, id,  module, NULL, NOW(), NOW(), NULL, NULL
FROM tmp_daily");
    } else {
        print "<p>Deja fait</p>";
    }
}

$report_date = date('Y-m-d');
$res = $DATABASE->query( "SELECT * FROM cnc_daily_report WHERE daily_date = '$report_date'");
$report = $res->fetch(PDO::FETCH_ASSOC);
unset($rest);

print "<h1>Rapport du ".$report_date."</h1>";

if (isset($_POST['finalize'])) {
    $test_creation = intval($_POST['test_creation']);
    $progres = intval($_POST['progres']);
    // @todo use user! :)
    $DATABASE->query( "
UPDATE cnc_daily_report 
SET test_creation = '$test_creation', 
    progres = '$progres',
    date_submission = NOW(),
    date_final = NOW()
WHERE 
    daily_date = '$report_date' AND
    date_final = 0
");
} elseif (isset($_POST['keep'])) {
    $test_creation = intval($_POST['test_creation']);
    $progres = intval($_POST['progres']);
    $DATABASE->query("
UPDATE cnc_daily_report 
SET test_creation = '$test_creation', 
    progres = '$progres',
    date_submission = NOW()
WHERE 
    daily_date = '$report_date' AND
    date_final = 0
");
}

$res = $DATABASE->query("SELECT id, reason FROM cnc_reasons");
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

$select = "<option value=\"0\">Select a reason</option>\n".            "<option value=\"-1\">Autre</option>\n";
foreach($rows as $row) {
    $select .= "<option value=\"{$row['id']}\">".htmlentities($row['reason'], ENT_COMPAT, 'ISO-8859-1')."</option>\n";
}

$res = $DATABASE->query("
SELECT CR.id AS id, CR.module, element, CR.fichier, line, reason_id 
FROM cnc_daily DL
JOIN cnc_rapport CR
    ON CR.id = DL.report_id
JOIN cnc T1
    ON T1.id = CR.token_id
WHERE date_report = CURDATE()");


print "<form action=\"create_daily.php\" method=\"POST\" name=\"mon_form\">";
print "<table>";
while($row = $res->fetch(PDO::FETCH_ASSOC)) {
    $row['reason'] = "<select name=\"select_{$row['id']}\" OnChange=\"javascript:checkElementId('tr_{$row['id']}', '".$row['id']."', '".$row['module']."', this.selectedIndex )\">".
            str_replace("value=\"{$row['reason_id']}\"","value=\"{$row['reason_id']}\" selected=\"selected\"", $select).
            "</select>";
    if ($row['reason_id'] > 0) {
        print "<tr class=\"checked\" id=\"tr_{$row['id']}\">";
    } else {
        print "<tr class=\"e\" id=\"tr_{$row['id']}\">";
    }
//    unset($row['id']);
    unset($row['reason_id']);
    print "<td>".join("</td><td>", $row)."</td>";
    print "<td><div id=\"tr_{$row['id']}_0\"></div></td>";
    print "</tr>";
}
print "</table>";

print "Avez-vous eu le temps de faire des tests ? ";
$tests = array('Choisissez', '0','1','2','5','10','20+');
print "<select name=\"test_creation\">";
print make_select($tests, $report['test_creation']);
print "</select>";

print "<br />";

print "Avez-vous bien progressé dans le code ? ";
$progres = array('Choisissez',  'Très bien','Bien','Peu','Très mal');
print "<select name=\"progres\">";
print make_select($progres, $report['progres']);
print "</select>";
print "<br />";
print "<input type=\"submit\" name=\"finalize\" value=\"Fermer le rapport\" />";
print "<input type=\"submit\" name=\"keep\" value=\"Terminer plus tard\" />";
print "</form>";

print print_pieddepage();

function make_select($list, $selected) {
    $select = "";
    foreach($list as $id => $row) {
        if ($id == $selected) {
            $select .= "<option value=\"$id\" selected=\"selected\">".htmlentities($row, ENT_COMPAT, 'UTF-8')."</option>\n";
        } else {
            $select .= "<option value=\"$id\">".htmlentities($row, ENT_COMPAT, 'UTF-8')."</option>\n";
        }
    }
    return $select;
}

function make_radio($list, $name) {
    $radio = "";
    foreach($list as $id => $row) {
        $radio .= "<input name=\"$name\" value=\"$id\" />".htmlentities($row, ENT_COMPAT, 'ISO-8859-1')."&nbsp;\n";
    }
    return $radio;
}

?>