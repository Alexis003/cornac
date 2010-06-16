<?php

function defined_and_used() {}
defined_and_used();

function defined_but_not_used() {}

// fontion PHP, doit être ignorée
eval("");
unset($x);

?>