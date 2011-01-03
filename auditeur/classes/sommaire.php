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

class sommaire {
    private $modules = array();
    
	function __construct() {

	}

    function add($module) {
        $this->modules[] = $module;
    }

    function sauve() {
        $html = '';
        $files = scandir('export');
        sort($files);
        setlocale(LC_TIME, "fr_FR");
        $date = strftime("%A %d %B %Y %H:%M:%S ");
        
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                              "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
        <head>
         <title>Audit de code PHP</title>
         <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        </head>
        <body>
      <!--  <p><a href="#">Code source à télécharger</a></p> -->
        <p><a href="liste.txt">Liste des files</a></p>
        
        <table>';
        $html .= "<tr>
            <td>Production</td>
            <td>$date</td>
        </tr>
        </table>";
        
        $html .= "
        <table>
        <tr>
            <td>Nom</td>
            <td>Nombre</td>
            <td>Description</td>
        </tr>
        ";
        
        
        foreach($this->modules as $module) {
            
            $f = $module->getfilename();
            if ($f[0] == '.') { continue; }
            if (in_array($f[0], array('index.html'))) { continue; }
        
            $n = $module->getnombre();
            $d = $module->getdescription();
        
            $html .= "<tr>
            <td><a href=\"$f\">$f</a></td>
            <td>$n</td>
            <td>$d</td>
        </tr>";
        }
        
        $html .= '</table></body></html>';
        file_put_contents('export/index.html', $html);
        
    }	
}
?>