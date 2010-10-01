<?php

class export_php {
    
    function __construct($comments) {
        // @todo check for incoming array !
        $this->comments = $comments;
    }
    
    function save($filename) {
        if (empty($filename)) {
            print var_export($this->comments);
            return true; 
        }
        
        if ($filename == __FILE__) {
            $filename .= '_php';
        }
        $fp = fopen($filename.'.php', 'w+');
        if (!is_resource($fp)) {
            display_message("Couldn't open $filename for output. Ignoring");
            return false;
        }
        
        fwrite($fp, "<"."?php \n\$comment = ".var_export($this->comments, true)."; ?".">");
        fclose($fp);
        
        return true;
    }

}

?>