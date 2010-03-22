<?php
    foreach($keys as $keypos => $key) array_splice($address, $key - ($keypos * 2 + 1), 2);
?>