<?php

function defined_and_used() {}
defined_and_used();

used_but_undefined();

// fontion PHP, doit être ignorée
eval("");
unset($x);

?>