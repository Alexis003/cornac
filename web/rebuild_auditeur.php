<?php

include('include/config.php');

$analyzer = $_GET['analyzer'];

$res = $DATABASE->query('UPDATE <tasks> SET completed=0 
                            WHERE target='.$DATABASE->quote($analyzer).' AND
                                  task = "auditeur"');

// @todo Also clean reports
// @todo also clean token tables

header('Location: status_auditeur.php');
die();
?>