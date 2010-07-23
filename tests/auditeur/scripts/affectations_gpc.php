<?php

$_GET['x'] = 1;
$y = $x[$_GET['y']];
$y = test($_GET);
$y = test($_FILES);
$y = test($_NORMAL  );
$_GET['z'][] = $_POST['a'];
$_GET['a'] = $_NORMAL['b'];
$z = $_SERVER['c']['d'];
$za = $_ENV['e'];
$zb = $_env['e'];
$zc = $_FILES['f'];
$zd = $HTTP_GET_VARS['g'];
$ze = $HTTP_POST_VARS["h"];
?>