#!/usr/bin/env php
<?php



$args = $argv;
array_shift($args);

while($arg = array_shift($args)) {
    if (!is_numeric($arg)) {
        if (defined($arg)) {
            print "$arg ".constant($arg)."\n";
        } else {
            print "$arg UNKNOWN\n";
        }
    } else {
        print "$arg ".token_name($arg)."\n";
    }
}

?>