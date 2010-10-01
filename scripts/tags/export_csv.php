<?php

class export_csv {
    
    function __construct($comments) {
        // @todo check for incoming array !
        $this->comments = $comments;
    }
    
    function save($filename) {
        if (empty($filename)) {
            print $this->make_csv();
            return true; 
        }
        
        if ($filename == __FILE__) {
            $filename .= '_php';
        }
        $fp = fopen($filename.'.csv', 'w+');
        if (!is_resource($fp)) {
            display_message("Couldn't open $filename for output. Ignoring");
            return false;
        }
        
        fwrite($fp,  $this->make_csv());
        fclose($fp);
        
        return true;
    }
    
    function make_csv() {
        $csv = '';
        
        foreach($this->comments as $comment) {
            $comment['tags'] = join(', ', $comment['tags']);
            unset($comment['raw']);
            $csv .= '"'.join('","', $comment)."\"\n";
        }
        
        return $csv;
    }

}

?>