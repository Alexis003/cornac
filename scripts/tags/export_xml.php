<?php

class export_xml {
    
    public function __construct($comments) {
        // @todo check for incoming array !
        $this->comments = $comments;
    }
    
    public function save($filename) {
        if (empty($filename)) {
            print $this->make_xml();
            return true; 
        }
        
        if ($filename == __FILE__) {
            $filename .= '_php';
        }
        $fp = fopen($filename.'.xml', 'w+');
        if (!is_resource($fp)) {
            display_message("Couldn't open $filename for output. Ignoring");
            return false;
        }
        
        fwrite($fp,  $this->make_xml());
        fclose($fp);
        
        return true;
    }
    
    private function make_xml() {
        $xml = '';
        
        foreach($this->comments as $id => $comment) {
//            $comment['tags'] = join(', ', $comment['tags']);
            unset($comment['raw']);
            
            $comment_xml = '';
            foreach($comment as $tag => $value) {
                if ($tag == 'tags') {
                    $tags = "      <tag>".join("</tag>\n    <tag>", $value)."</tags>";
                    $comment_xml .= "    <$tag count=\"".count($value)."\">\n$tags\n    </$tag>\n";
                } else {
                    $comment_xml .= "    <$tag>".htmlspecialchars($value, ENT_COMPAT, 'UTF-8')."</$tag>\n";
                }
            }
            
            $xml .= "  <comment id=\"$id\">\n$comment_xml  </comment>\n";
        }
        
        return "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<list count=\"".count($this->comments)."\">\n$xml\n</list>\n";
    }

}

?>