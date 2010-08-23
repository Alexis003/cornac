<?php

$list = array();

if (in_array('-m', $argv)) {
    $t = parse_ini_file( '../dict/functions2ext.ini', true);
    
    foreach($t as $ext => $functions) {
        if (isset($functions['function'])) {  
            $list[strtolower($ext)] = $functions['function'];        
        } else {
            $list[strtolower($ext)] = array();
        }
    }
}

// bulk of them
$trouvees = array();
$exts = get_loaded_extensions();

foreach($exts as $ext) {
    $ext = strtolower($ext);
    $f = get_extension_funcs($ext);

    if (!is_array($f)) { 
        $f = array();
    }
    
    sort($f);
    $list[$ext] = $f;
    $trouvees = array_merge($trouvees, $f);
}

// special cases : missing ones
$df = get_defined_functions();
$missing = array_diff($df['internal'], $trouvees);

$list['core'] = array_merge($list['core'], $missing);
$trouvees = array_merge($trouvees, $missing);

$missing = array_diff($df['internal'], $trouvees);

// those are not functions for PHP, but they are mostly used as is
$extras = array('echo','print','die','exit','isset','empty','array','list','unset','eval');
$list['core'] = array_merge($list['core'], $extras);

ksort($list);
sort($list['core']);

// output
$ini = '';
foreach($list as $ext => $f) {
    $g = "function[] = ".join("\nfunction[] = ", $f)."\n";
    $g = strtolower($g);
    $ini .= "[$ext]
$g
";
}


if (in_array('-m', $argv)) {
    print "merging into functions2ext.ini\n";
    
    $old = filesize('../dict/functions2ext.ini');
    if (strlen($ini) > $old) {
        print "New functions added\n";
    } else {
        print "NO new functions added\n";
    }
    file_put_contents('../dict/functions2ext.ini', $ini);
} else {
    print $ini;
}
?>