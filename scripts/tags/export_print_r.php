<?php

class export_print_r {
    
    function __construct($comments) {
        // @todo check for incoming array !
        $this->comments = $comments;
    }
    
    function save($filename) {
        print_r($this->comments);
    }

}

?>