<?php

// should be spotted
if (strpos($a, $b)) { }
while (strpos($aw, $bw)) { }

// should be spotted
if (strpos($c, $d) == 0) { }
if (0 == strpos($g, $h)) { }

// shouldn't be spotted
if (strpos($e, $f) === 0) { }

?>