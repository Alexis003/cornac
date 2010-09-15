<?php

function get_html($lines) {
    
    
    
    print __METHOD__;
}

function get_html_level2($lines) {
    global $mysql, $tables;
    
    include('../libs/write_ini_file.php');

    print $requete = "SELECT DISTINCT concat(fichier,';','white') AS all_files FROM {$tables['<tokens>']} ";
    $res = $mysql->query($requete);
    $rows = pdo_fetch_one_col($res);
    
    include('../libs/file2png.php');
    
    $image = new file2png();
    $image->setArray($rows);
    $image->process();
    $image->save();
    
    print_r($rows);

//    print_r($lines);
    print __METHOD__;
}        
    
function print_entete($prefix='No Name') {

}

function print_pieddepage($prefix='No Name') {

}
?>