#!/usr/bin/php
<?php

include('libs/getopts.php');
$args = $argv;

$help = get_arg($args, '-?') ;
if ($help) { help(); }

$origin = get_arg_value($args, '-d', null);
if (is_null($origin)) { help(); }

$destination = get_arg_value($args, '-o', null);
if (is_null($destination)) { help(); }

if (!file_exists($origin)) {
    print "Source folder '$origin' doesn't exist\n";
    die();
}

if (!file_exists($destination)) {
    print "Output folder '$destination' doesn't exist\n";
    die();
}

if (realpath($origin) == realpath($destination)) {
    print "Please, don't use the same folder for source and destination\n";
    die();
}

print "Dossier : $origin 
Output : $destination\n";

shell_exec("./tokenizeur.php -r -d $origin -g mysql,cache > /dev/null"); // @todo : note the log 
                                                                         // @sqlite as default ? 
shell_exec("cd auditeur; ./auditeur.php -p tokens -o html");

shell_exec("rm -rf /tmp/cornac; mkdir /tmp/cornac");

shell_exec("cd auditeur; ./lecture_module.php -p tokens -F html -o /tmp/cornac ");

print "Done\n";

function help() {
    print <<<SHELL
Usage : ./cornac -d <source folder> -o <output folder>

Options : 
  -? : help
  -d : source folder
  -o : output folder
SHELL;
    die();
}

?>