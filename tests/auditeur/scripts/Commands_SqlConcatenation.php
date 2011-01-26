<?php


$x  =  'SELECT * FROM '.$table;
$x1  =  'DELETE FROM '.$table;
$x2  =  'UPDATE '.$table.' SET x = 1';
$a .= 'Where X =1  /* non concatenation */';

$b = 'other string non SQL';

?>