#!/usr/bin/php 
<?php

print_r(Liste_directories_recursive('References/iws_multi'));

function Liste_directories_recursive( $path = '.', $level = 0 ){ 
    $ignore = array( 'cgi-bin', '.', '..' ); 

    $dh = opendir( $path ); 
    $retour = array();
    while( false !== ( $file = readdir( $dh ) ) ){ 
        if( !in_array( $file, $ignore ) ){ 
            if( is_dir( "$path/$file" ) ){ 
                $r = Liste_directories_recursive( "$path/$file", ($level+1) ); 
                $retour = array_merge($retour, $r);
            } else { 
                $retour[] = "$path/$file";
            } 
        } 
    } 
     
    closedir( $dh ); 
    return $retour;
} 

?>