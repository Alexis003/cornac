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