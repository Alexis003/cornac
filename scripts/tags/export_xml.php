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