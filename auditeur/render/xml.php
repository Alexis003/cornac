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

class Render_xml {
    
    function render($lines) {
        $return = "<?xml version=\"1.0\" encoding=\"UTF-8\"?".">\n";
        if (count($lines) == 0) {
            $return .= "<document />";
            return $return;
        }

        $return .= "<document count=\"".count($lines)."\">\n";
        foreach($lines as $id => $line) {
            $return .= "  <ligne id=\"$id\">\n";
            foreach($line as $col => $value) {
                $return .= "    <$col>".htmlentities($value)."</$col>\n";
            }
            $return .= "  </ligne>\n";
        }
        $return .= "</document>";

        return $return; 
    }
}

?>