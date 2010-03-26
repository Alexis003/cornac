<?php

class template_none extends template {
    
    function __construct($root) {
        parent::__construct();
    }
    
    function save($filename = null) {
        return false;
    }
    
    function affiche($noeud = null, $niveau = 0) {
        return;
    }
}
?>