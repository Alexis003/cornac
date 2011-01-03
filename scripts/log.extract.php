#!/usr/bin/env php
<?php




$file = file_get_contents('reference.log');

$total = preg_match_all("#Cycles = -1#i", $file, $r);

preg_match_all("#\n(.*)\n(.*)\nil reste (\d+) #i", $file, $r);

$trouve = count($r[1]);

print $trouve." fichiers sur un total de $total (".number_format(($total - $trouve)/$total * 100, 2)." %), pour ".array_sum($r[3])." Tokens\n";

foreach($r[1] as $x) {
    print "./tokenizeur.php -S -l -f $x -e | bbedit\n";
}

?>