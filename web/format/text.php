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
    
function print_entete($prefix='No Name') {

}

function print_pieddepage($prefix='No Name') {

}
?>