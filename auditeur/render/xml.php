<?php

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