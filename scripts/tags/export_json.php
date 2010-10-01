<?php

class export_json {
    
    function __construct($comments) {
        // @todo check for incoming array !
        $this->comments = $comments;
    }
    
    function save($filename) {
        if (empty($filename)) {
            print json_encode($this->comments);
            return true; 
        }
        
        if ($filename == __FILE__) {
            $filename .= '_php';
        }
        $fp = fopen($filename.'.js', 'w+');
        if (!is_resource($fp)) {
            display_message("Couldn't open $filename for output. Ignoring");
            return false;
        }
        
        fwrite($fp, json_encode($this->comments));
        fclose($fp);
        
        return true;
    }

}

?>