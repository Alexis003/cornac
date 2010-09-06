<?php

function get_html($lines) {
    $r = '';
    foreach($lines as $key => $line) {
        $r .= join("\t",$line)."\n";
    }
    return $r;
}

function get_html_level2($lines) {
    $r = '';
    foreach($lines as $key => $line) {
        foreach($line as $rows) {
            $r .= join("\t",$rows)."\n";
        }
    }
    return $r;
}        
    
function print_entete($prefixe='Sans Nom') {

}

function print_pieddepage($prefixe='Sans Nom') {

}
?>