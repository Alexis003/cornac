#!/usr/bin/env php
<?php

$php = file_get_contents('../auditeur/auditeur.php');

preg_match('#\$modules = array\((.*?)\);#is', $php, $r);

$analyzers = explode(',', $r[1]);
$analyzers = array_map('trim', $analyzers);
$analyzers = array_unique($analyzers);

$last = array_pop($analyzers);

sort($analyzers);
$analyzers[] = $last;

$code = join(",\n", $analyzers)."\n";
$code = preg_replace('#(\'[a-zA-Z]+\',)#s', "\n".'\1', $code);

print_r($code);

$php = preg_replace('#\$modules = array\((.*?)\);#is', '\$modules = array('.$code.');', $php);

file_put_contents('../auditeur/auditeur.php', $php);

?>