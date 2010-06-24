<?php

function defined_and_used_in_functions_undefined() {}
defined_and_used_in_functions_undefined();

used_but_undefined_in_functions_undefined();

// fontion PHP, doit être ignorée
eval("");
unset($x);

?>