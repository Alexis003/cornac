<?php

$tests = glob('./class.*.test.php');

foreach($tests as $id => $test) {
    $tests[$id] = substr($test,8, -9);
}


$scripts = glob('scripts/*');

foreach($scripts as $id => $script) {
    $scripts[$id] = substr($script,8, -4);
}

$diff = array_diff($tests, $scripts);
if(count($diff) != 0) {
    print "Some of the tests are missing their script : ".join(', ', $diff)."\n";
}

$diff = array_diff($scripts, $tests);
if(count($diff) != 0) {
    print "Some of the scripts are missing their tests : ".join(', ', $diff)."\n";
}


////////////////////////////////////////////////////////////////////////


$analyzers = glob('../../auditeur/classes/*.php');

foreach($analyzers as $id => $analyzer) {
    $analyzer = substr($analyzer,23, -4);
    
    if (in_array($analyzer, array('rendu'))) {
        unset($analyzers[$id]);
    } else {
        $analyzers[$id] = $analyzer;
    }
    
}

$diff = array_diff($analyzers, $tests);
if(count($diff) != 0) {
    print "Some of the analyzers are not tested : (".count($diff).") ".join(', ', $diff)."\n";
}

////////////////////////////////////////////////////////////////////////


$alltest = file_get_contents('alltests.php');
preg_match_all("/'class\.(.*?)\.test\.php',/", $alltest, $r);
$alltest = $r[1];

$diff = array_diff($tests, $alltest);
if(count($diff) != 0) {
    print "Some of the tests are not in all tests : (".count($diff).") \n'class.".join(".test.php',\n'class.", $diff).".test.php',\n";
}


?>