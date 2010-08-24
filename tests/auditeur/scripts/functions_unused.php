<?php

function defined_and_used() {}
defined_and_used();

function defined_but_not_used() {}

// PHP functions, must be ignored
eval("");
unset($x);

// defined, but used by new. Must be ignored
function __autoload($class) {
}

?>