<?php
    function a(b &$c, &$d, &$e, &$f, array &$g) {}
    function b(b &$c, &$d, &$e, $f = 3, array &$g = array()) {}
    function c(b &$c, &$d, &$e, $f, array &$g = array()) {}
    function d(b &$c, &$d, &$e, array $f, array &$g = array()) {}
?>