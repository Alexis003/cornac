<?php

echo strtolower(&$x);

function dontspot(&$y) {}

userland_function($a, $b, &$c);




?>