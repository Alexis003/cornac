<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
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
include('./format/lib_html.php');

function get_html_check($lines, $module) {
        $table = new html_table();

        $distinct = count($lines);
        $total = 0;

        foreach($lines as $id_tr => $line) {
            $total += $line['nb'];
            
            $line['element'] = htmlentities($line['element'], ENT_COMPAT, 'UTF-8');
            if (isset($line['link'])) {
                $line['element'] = "<a href=\"{$line['link']}\" title = \"{$line['element']}\">".$line['element']."</a>";
            }

            if ($line['checked']) {
                $row = $table->addRow(array());
                $id = $row->getId();
                $row->setCells(array($line['element'], 
                               $line['nb'], 
                               '<input type="checkbox" checked OnClick="javascript:checkElement( \''.$id.'\','.$line['id'].',\''.$module.'\');">'
                               ));
                $row->setCellsClass('checked');
             } else {
                $row = $table->addRow(array());
                $id = $row->getId();
                $row->setCells(array($line['element'], 
                                     $line['nb'], 
                                     '<input type="checkbox" OnClick="javascript:checkElement(\''.$id.'\','.$line['id'].',\''.$module.'\');">'
                               ));
                $row->setCellsClass(array('e','v','v'));
             }
        }

        $row = $table->InsertRow(0, array('Total', $total, ''));
        $row->setCellsClass('h');

        $row = $table->InsertRow(0, array('Distinct', $distinct, '' ));
        $row->setCellsClass('h');

        $row = $table->addRow( array('Distinct', $distinct, '' ));
        $row->setCellsClass('h');
        
        $row = $table->addRow(array('Total', $total, ''));
        $row->setCellsClass('h');

        global $prefix;
        return print_entete($prefix).$table->asHTML().print_pieddepage($prefix);
}   

function get_html($lines) {
        $table = new html_table();

        $distinct = count($lines);
        $total = 0;

        foreach($lines as $id_tr => $line) {
            $total += $line['nb'];
            
            $line['element'] = htmlentities($line['element'], ENT_COMPAT, 'UTF-8');
            if (isset($line['link'])) {
                $line['element'] = "<a href=\"{$line['link']}\" title = \"{$line['element']}\">".$line['element']."</a>";
            }

            if ($line['checked']) {
                $row = $table->addRow(array($line['element'], 
                                     $line['nb']
                                     ));
                $table->setCellsClass($row, 'checked');
             } else {
                $row = $table->addRow(array($line['element'], 
                                     $line['nb']
                                     ));
                $row->setCellsClass(array('e','v'));
             }
        }

        $row = $table->InsertRow(0, array('Total', $total));
        $row->setCellsClass('h');

        $row = $table->InsertRow(0, array('Distinct', $distinct ));
        $row->setCellsClass('h');

        $row = $table->addRow( array('Distinct', $distinct ));
        $row->setCellsClass('h');
        
        $row = $table->addRow(array('Total', $total));
        $row->setCellsClass('h');

        global $prefix;
        return print_entete($prefix).$table->asHTML().print_pieddepage($prefix);
}

function get_html_manual($lines) {
        $distinct = count($lines);
        $total = 0;
        
        $html = '';
        foreach($lines as $id_tr => $line) {
            $line['element'] = htmlentities($line['element'], ENT_COMPAT, 'UTF-8');
            if (isset($line['link'])) {
                $line['element'] = "<a href=\"{$line['link']}\" title = \"{$line['element']}\">".$line['element']."</a>";
            }
            $html .= 
'<tr>
    <td class="e" id="tr_'.$id_tr.'">'.$line['element'].'</td>
    <td class="v" id="tr_'.$id_tr.'_2">'.$line['nb'].'</td>
    <td class="v" id="tr_'.$id_tr.'_2">'.$line['todo'].'</td>
</tr>';
            $total += $line['nb'];
        }

        $html = '<table border="0" cellpadding="3" width="600">'.
                '<tr><td class="h">Total</td><td class="h">'.$total.'</td><td class="h"></td></tr>'.
                '<tr><td class="h">Distinct</td><td class="h">'.$distinct.'</td><td class="h"></td></tr>'.$html;

        $html .= '<tr><td class="h">Total</td><td class="h">'.$total.'</td><td class="h"></td></tr>';
        $html .= '<tr><td class="h">Distinct</td><td class="h">'.$distinct.'</td><td class="h"></td></tr>';
        $html .= "</table><div id=\"myDiv\" />";

        global $prefix;
        $html = print_entete($prefix).$html.print_pieddepage($prefix);
        
        return $html;
}

function get_html_level2($lines,$module='inclusions_path') {
    $total = count($lines);
    
    $table = new html_table();

    $id_tr = 0;
    foreach($lines as $file => $rows) {
        $row = $table->addRow(array($file, 
                                    count($rows),
                                    '<input type="checkbox">'
                              ));
        $row->setCellsClass('h');
        $row->getCell(0)->setAttribute('OnClick', 'toggle_row('.(substr($row->getId(), 2) + 1).', '.count($rows).');' );
        foreach($rows as $row) {
            $id_tr++;

            if (@$row['checked']) {
                $tr_row = $table->addRow(array());
                $id = $tr_row->getId();
                $tr_row->setCells(array($row['element'], 
                                     $row['nb'],
                                     '<input type="checkbox" checked OnClick="javascript:checkElementFile(\''.$id.'\','.$row['id'].',\''.$module.'\');" />'
                                     ));
                $tr_row->setCellsClass( 'checked');
             } else {
                $tr_row = $table->addRow(array());
                $id = $tr_row->getId();
                $tr_row->setCells(array($row['element'], 
                                      $row['nb'], 
                                     '<input type="checkbox" OnClick="javascript:checkElementFile(\''.$id.'\','.$row['id'].',\''.$module.'\');" />'
                                      ));
                $tr_row->setCellsClass(array('e','v','v'));
             }
            $tr_row->setStyle('display','none');
        }
    }

    $table->InsertRow(0, array('Total', $total,''))
          ->setCellsClass('h');

    $row = $table->addRow(array('Total', $total,''))
                 ->setCellsClass('h');

    global $prefix;
    return print_entete($prefix).$table->asHTML().print_pieddepage($prefix);
}        

    
function print_entete($prefix = 'No Name') {
    global $entete;
    
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
 <title>Analyseur pour l'application $prefix</title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
 <script type="text/javascript" src="site.js"></script>
 <style type="text/css" media="all">
  @import url("./css/site.css");
 </style>
</head>
<body>

<a href="index.php">Index</a> $entete
HTML;

}

function print_pieddepage($prefix = 'No Name') {
    return <<<HTML
    <p>
    <a href="#" onClick="javascript:document.cookie = 'langue=fr';window.location.reload();">fr</a> - 
    <a href="#" onClick="javascript:document.cookie = 'langue=en';window.location.reload();">en</a>
    </p>
    </body>
</html>
HTML;
}
?>