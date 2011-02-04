<?php

include('include/config.php');

$filename = $_GET['file'];

$res = $DATABASE->query('UPDATE <tasks> SET completed=0 
                            WHERE target='.$DATABASE->quote($filename).'');

// @todo Also clean reports
// @todo also clean token tables

header('Location: status_file.php');
die();
?>